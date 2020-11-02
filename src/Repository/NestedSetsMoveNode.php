<?php

namespace MartenaSoft\Common\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\Common\Entity\NestedSetEntityInterface;


class NestedSetsMoveNode
{
    private const MOVE_TMP_TABLE = '_move_tmp';
    private const MOVE_TMP_TABLE_ALL_NODES = '_move_tmp_all_nodes';
    private EntityManagerInterface $entityManager;
    private ?Connection $connection = null;
    private NestedSetServiceRepositoryInterface $nestedSetServiceRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        NestedSetServiceRepositoryInterface $nestedSetServiceRepository
    ) {
        $this->entityManager = $entityManager;
        $this->nestedSetServiceRepository = $nestedSetServiceRepository;
        $this->connection = $this->entityManager->getConnection();
    }

    public function move(NestedSetEntityInterface $node, ?NestedSetEntityInterface $parent): void
    {
        $connection = $this->getConnection();
        $moveTmpTable = $this->getMovedTemporaryTableName();
        $nsTableName = $this->nestedSetServiceRepository->getTableName();
        $tmpAllNodesTableName = $this->getMovedTemporaryTableNameForAllNodes();

        $this->deleteTemplateTables();

        $sql = "CREATE TABLE IF NOT EXISTS `{$moveTmpTable}` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,                
                `lft` int unsigned DEFAULT NULL,
                `rgt` int unsigned DEFAULT NULL,          
                `tree` int unsigned DEFAULT NULL,          
                `parent_id` int unsigned DEFAULT NULL,
                PRIMARY KEY (`id`)); ";

        $treeIdArray = [
            $node->getTree()
        ];

        if (!empty($parent) && $parent->getTree() != $node->getTree()) {
            $treeIdArray[] = $parent->getTree();
        }

        $sql .=  " CREATE TABLE `{$tmpAllNodesTableName}` 
                SELECT `ns`.`id`, 
                       `ns`.`parent_id`, 
                       `ns`.`lft`, 
                       `ns`.`rgt`, 
                       `ns`.`tree`, 
                       `ns`.`lvl`,
                       0 i 
                    FROM `{$nsTableName}` `ns` 
                WHERE `ns`.`tree` IN (" . implode(',', $treeIdArray) . ");";

        $connection->executeQuery($sql);

        $connection->beginTransaction();
        $throwException = null;

        try {
            $sql = "INSERT INTO `{$moveTmpTable}` 
                    SELECT `ns`.`id`, `ns`.`lft`, `ns`.`rgt`, `ns`.`tree`, `ns`.`parent_id` FROM `{$nsTableName}` `ns` 
                       WHERE `ns`.`lft` >= {$node->getLft()}
                          AND `ns`.`rgt` <= {$node->getRgt()}
                          AND `ns`.`tree` = {$node->getTree()}
                      ORDER BY `ns`.`lft` ";

            $insertedLength = (int)$connection->exec($sql);

            if ($insertedLength == 0) {
                throw new \Exception('Inserted move users length is 0', 4);
            }

            $insertedLength *= 2;

            $sql = $this
                    ->nestedSetServiceRepository
                    ->getDeleteQuery($node, $tmpAllNodesTableName);

            $connection->executeQuery($sql);

            if ($parent !== null) {
                $parentNew = $connection
                    ->fetchAssoc("SELECT * FROM {$tmpAllNodesTableName} WHERE id=:id AND tree=:tree",
                        [
                            "id" => $parent->getId(),
                            "tree" => $parent->getTree()
                        ]);

                if (empty($parentNew)) {
                    throw new \Exception('Can`t find parent node ' . $parent->getId());
                }

                $sql = "UPDATE `{$tmpAllNodesTableName}` SET
                            lft = (
                                CASE
                                   WHEN lft > {$parentNew['lft']}
                                        AND rgt < {$parentNew['rgt']}
                                        AND tree = {$parentNew['tree']}
                                THEN lft + {$insertedLength}
                                
                                WHEN lft > {$parentNew['lft']}
                                     AND rgt > {$parentNew['rgt']}
                                     AND tree = {$parentNew['tree']}
                                THEN lft + {$insertedLength}
                                ELSE lft END
                            ),

                            rgt = (
                                CASE
                                    WHEN lft > {$parentNew['lft']}
                                         AND rgt < {$parentNew['rgt']}
                                         AND tree = {$parentNew['tree']}
                                    THEN rgt + {$insertedLength}
                                    
                                    WHEN (lft > {$parentNew['lft']}
                                         AND rgt > {$parentNew['rgt']}
                                         AND tree = {$parentNew['tree']}
                                         ) OR (lft <= {$parentNew['lft']}
                                         AND rgt >= {$parentNew['rgt']}
                                         AND tree = {$parentNew['tree']}
                                         ) 
                                    THEN rgt + {$insertedLength}
                                    ELSE rgt END
                                )
                        WHERE tree = {$parent->getTree()};";
                $sql .= "SET @s_ := 0;";
                $sql .= "INSERT INTO `{$tmpAllNodesTableName}` 
                        SELECT  id,
                                IF (@s_ = 0, {$parentNew['id']}, parent_id),
                                lft - {$node->getLft()} + 1 + {$parentNew['lft']}, 
                                rgt - {$node->getLft()} + 1 + {$parentNew['lft']},
                                {$parentNew['tree']},
                                 
                                ( 
                                    (SELECT COUNT(*) FROM {$moveTmpTable} t1 WHERE t1.lft < t2.lft AND t1.rgt>t2.rgt)  
                                    + {$parent->getLvl()} + 1
                                ),
                                @s_ := @s_ + 1
                                FROM {$moveTmpTable} t2;";

               $connection->executeQuery($sql);
            } else {
                $maxTree = $this->nestedSetServiceRepository->getMaxTree();
                $sql = "@s_ := 0;";
                $sql .= "INSERT INTO `{$tmpAllNodesTableName}` 
                        SELECT IF (@s_ = 0, 0, parent_id),
                               @s_ := 1,
                               lft - {$node->getLft()} + 1, 
                               rgt - {$node->getLft()} + 1,
                               " . ($maxTree + 1) . ",
                               (
                                    (SELECT COUNT(*) FROM user_front_moved_users_ns_tmp t1 
                                        WHERE t1.lft < t2.lft AND t1.rgt>t2.rgt)  + 1
                               )         
                        FROM {$moveTmpTable} t2";
                $connection->executeQuery($sql);
            }


            $this->migrateFromTemporaryTable();
            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();
            $throwException = $exception;
        }

        $this->deleteTemplateTables();

        if ($throwException instanceof \Throwable) {
            throw $throwException;
        }
    }

    public function getMovedTemporaryTableName(): string
    {
        return $this->nestedSetServiceRepository->getTableName() . '_' . self::MOVE_TMP_TABLE;
    }

    public function getMovedTemporaryTableNameForAllNodes(): string
    {
        return $this->nestedSetServiceRepository->getTableName() . '_' . self::MOVE_TMP_TABLE_ALL_NODES;
    }

    private function migrateFromTemporaryTable(): void
    {
        $connection = $this->getConnection();
        $allNodesTmpTableName = $this->getMovedTemporaryTableNameForAllNodes();
        $nsTableName = $this->nestedSetServiceRepository->getTableName();
        $sql = "UPDATE `{$nsTableName}` ns 
                    INNER JOIN {$allNodesTmpTableName} nst 
                    ON ns.id = nst.id 
                SET ns.lft = nst.lft, 
                    ns.rgt = nst.rgt, 
                    ns.tree = nst.tree, 
                    ns.lvl = nst.lvl, 
                    ns.parent_id = nst.parent_id";

        $connection->executeQuery($sql);
    }

    private function deleteTemplateTables(): void
    {
        $moveTmpTable = $this->getMovedTemporaryTableName();
        $tmpAllNodesTableName = $this->getMovedTemporaryTableNameForAllNodes();

        $connection = $this->getConnection();
        $sql = "DROP TABLE IF EXISTS `{$moveTmpTable}`;";
        $sql .= "DROP TABLE IF EXISTS `{$tmpAllNodesTableName}`;";
        $connection->executeQuery($sql);
    }

    private function getConnection(): Connection
    {
        return $this->connection;
    }

}
