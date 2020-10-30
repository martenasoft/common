<?php

namespace MartenaSoft\Common\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\Common\Repository\NestedSetServiceRepositoryInterface;

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
    }

    public function getMovedTemporaryTableName(): string
    {
        return $this->nestedSetServiceRepository->getTableName() . '_' . slef::MOVE_TMP_TABLE;
    }

    public function getMovedTemporaryTableNameForAllNodes(): string
    {
        return $this->nestedSetServiceRepository->getTableName() . '_' . slef::MOVE_TMP_TABLE_ALL_NODES;
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
                `parent_id` int unsigned DEFAULT NULL,
                PRIMARY KEY (`id`))";

        $treeIdArray = [
            $node->getTree()
        ];

        if (!empty($parent) && $parent->getTree() != $node->getTree()) {
            $treeIdArray[] = $parent->getTree();
        }

        $sql .=  "CREATE TABLE `{$tmpAllNodesTableName}` 
                SELECT `ns`.`id`, 
                       `ns`.`parent_id`, 
                       `ns`.`lft`, 
                       `ns`.`rgt`, 
                       `ns`.`tree`, 
                       `ns`.`lvl` 
                    FROM `{$nsTableName}` `ns` 
                WHERE `ns`.`tree` IN (" . implode(',', $treeIdArray) . ")";

        $connection->executeQuery($sql);
        $connection->beginTransaction();
        $throwException = null;

        try {
            $sql = "INSERT INTO `{$moveTmpTable}` 
                    SELECT NULL, `ns`.`id`, `ns`.`lft`, `ns`.`rgt`, `ns`.`parent_id` FROM `{$nsTableName}` `ns` 
                WHERE `ns`.`lft` >= " . $node->getLft()
                . ' AND `uf`.`rgt` <= ' . $node->getRgt()
                . ' AND `uf`.`tree` = ' . $node->getTree()
                . ' ORDER BY `uf`.`lft` ASC';

            $connection->executeQuery($sql);
            $insertedLength = (int)$connection
                    ->executeQuery("SELECT COUNT(*) FROM `{$moveTmpTable}`")
                    ->fetchColumn(0) * 2;

            if ($insertedLength == 0) {
                throw new \Exception('Inserted move users length is 0', 4);
            }

            $this
                ->nestedSetServiceRepository
                ->delete($node, $tmpAllNodesTableName, $connection);

            if ($parent !== null) {
                $parentNew = $connection
                    ->fetchAssoc("SELECT * FROM {$tmpAllNodesTableName} WHERE id=:id AND tree=:tree",
                        [
                            "id" => $parent->getId(),
                            "tree" => $parent->getTree()
                        ]);

                if (empty($parentNew)) {
                    throw new \Exception('Can`t find upliner ' . $parent->getId());
                }

                $sql = "UPDATE `{$tmpAllNodesTableName}` SET
                            lft = (
                                CASE
                                   WHEN lft > " . $parentNew['lft'] .
                    " AND rgt < " . $parentNew['rgt'] .
                    " AND tree = " . $parentNew['tree'] .
                    " THEN lft + " . $insertedLength . "
                                        
                                        
                                   WHEN lft > " . $parentNew['lft'] .
                    " AND rgt > " . $parentNew['rgt'] .
                    " AND tree = " . $parentNew['tree'] .
                    " THEN lft + " . $insertedLength . "
                                   ELSE lft END
                            ),

                            rgt = (
                                CASE
                                    WHEN lft > " . $parentNew['lft'] .
                    " AND rgt < " . $parentNew['rgt'] .
                    " AND tree = " . $parentNew['tree'] . "
                                    THEN rgt+ " . $insertedLength . "
                                    
                                    WHEN (lft > " . $parentNew['lft'] .
                    " AND rgt > " . $parentNew['rgt'] .
                    " AND tree = " . $parentNew['tree'] .
                    ") OR (lft <= " . $parentNew['lft'] .
                    "      AND rgt >= " . $parentNew['rgt'] .
                    "      AND tree = " . $parentNew['tree'] .
                    ") 
                                    THEN rgt + " . $insertedLength . "
                                ELSE rgt END
                            )
                  WHERE tree = " . $parent->getTree();

                $connection->executeQuery($sql);
                $sql = "INSERT INTO `{$tmpAllNodesTableName}` 
                        SELECT user_id, 
                                IF (id = 1 OR upliner_id IS NULL, " . $parentNew['id'] . ", parent_id),
                                lft - (" . $node->getLft() . " + 1 + " . $parentNew['lft'] . ") , 
                                rgt - (" . $node->getLft() . " + 1 + " . $parentNew['lft'] . ") ," .
                    $parentNew['tree'] . ", 
                                ( 
                                    SELECT COUNT(*) FROM {$moveTmpTable} t1 WHERE t1.lft < t2.lft AND t1.rgt>t2.rgt  
                                    + " . $parent->getLvl() . " 
                                    + 1
                                )
                                FROM {$moveTmpTable} t2";

                $connection->executeQuery($sql);
            } else {
                $maxTree = $this->nestedSetServiceRepository->getMaxTree();
                $sql = "INSERT INTO `{$tmpAllNodesTableName}` 
                        SELECT IF (id = 1, NULL, parent_id),
                               lft - " . $node->getLft() . " + 1, 
                               rgt - " . $node->getLft() . " + 1,
                               " . ($maxTree + 1) . ",
                               (
                                    SELECT COUNT(*) FROM user_front_moved_users_ns_tmp t1 
                                        WHERE t1.lft < t2.lft AND t1.rgt>t2.rgt  + 1
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

    private function migrateFromTemporaryTable(): void
    {
        $connection = $this->getConnection();
        $tableName = $this->getMovedTemporaryTableName();
        $allNodesTmpTableName = $this->getMovedTemporaryTableNameForAllNodes();
        $sql = "UPDATE `{$tableName}` ns 
                    INNER JOIN $allNodesTmpTableName nst 
                    ON ns.id = nst.id 
                SET ns.lft = nst.lft, 
                    ns.rgt = nst.rgt, 
                    ns.tree = nst.tree, 
                    ns.lvl = nst.lvl, 
                    ns.parent_id = nst.parent_id";

        try {
            $connection->executeQuery($sql);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 30);
        }
    }

    private function deleteTemplateTables(): void
    {
        $moveTmpTable = $this->getMovedTemporaryTableName();
        $nsTableName = $this->nestedSetServiceRepository->getTableName();
        $connection = $this->getConnection();
        $sql = "DROP TABLE IF EXISTS `{$nsTableName}`;";
        $sql .= "DROP TABLE IF EXISTS `{$moveTmpTable}`;";
        $connection->executeQuery($sql);
    }

    private function getConnection(): Connection
    {
        return $this->connection;
    }
}