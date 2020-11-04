<?php

namespace MartenaSoft\Common\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use MartenaSoft\Common\Entity\NestedSetEntityInterface;
use MartenaSoft\Common\Entity\SafeDeleteEntityInterface;
use MartenaSoft\Common\Exception\ElementNotFoundException;

abstract class AbstractNestedSetServiceRepository extends ServiceEntityRepository
    implements NestedSetServiceRepositoryInterface, SafeRepositoryDeleteInterface
{
    use SafeDeleteTrait;

    protected string $alias = 'ns';

    public function getMaxTree(): int
    {
        $ret = (int)$this
            ->createQueryBuilder($this->alias)
            ->select("MAX({$this->alias}.tree)")
            ->getQuery()
            ->getSingleScalarResult();
        return ++$ret;
    }

    public function get(string $name): ?NestedSetEntityInterface
    {
        return $this->getItemQueryBuilder()
            ->andWhere("{$this->alias}.name=:name")
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getItemsQueryBuilder(NestedSetEntityInterface $nestedSetEntity): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder($this->alias);
        $queryBuilder->andWhere("{$this->alias}.lft>:lft")
            ->setParameter('lft', $nestedSetEntity->getLft());

        $queryBuilder->andWhere("{$this->alias}.rgt<:rgt")
            ->setParameter('rgt', $nestedSetEntity->getRgt());

        $queryBuilder->andWhere("{$this->alias}.tree=:tree")
            ->setParameter('tree', $nestedSetEntity->getTree());

        return $queryBuilder;
    }

    protected function getItemQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder($this->alias);
        return $queryBuilder;
    }

    public function getTableName(): string
    {
        return $this->getClassMetadata()->getTableName();
    }

    public function create(
        NestedSetEntityInterface $nestedSetEntity,
        ?NestedSetEntityInterface $parent = null
    ): NestedSetEntityInterface {
        if (!empty($nestedSetEntity->getId())) {
            return $nestedSetEntity;
        }

        $this->getEntityManager()->beginTransaction();

        try {
            if ($parent) {

                $lft = $parent->getLft();
                $rgt = $parent->getRgt();
                $lvl = $parent->getLvl();
                $parentId = $parent->getId();
                $tree = $parent->getTree();

                $tableName = $this->getTableName();
                $sql = "UPDATE $tableName {$this->alias} 
                            SET {$this->alias}.rgt={$this->alias}.rgt + 2 
                         WHERE {$this->alias}.tree=:tree AND {$this->alias}.rgt>=:lft;";

                $sql .= "UPDATE $tableName {$this->alias} 
                            SET {$this->alias}.lft={$this->alias}.lft + 2 
                         WHERE {$this->alias}.tree=:tree AND {$this->alias}.lft>:lft;";

                $lft = $rgt;
                $rgt++;
                $lvl++;

                $params = [
                    'tree' => $tree,
                    'lft' => $lft,
                    'rgt' => $rgt
                ];

                $this->getEntityManager()->getConnection()->executeQuery($sql, $params);

            } else {
                $tree =  $this->getMaxTree();
                $lft = $lvl = 1;
                $rgt = 2;
                $parentId = 0;
            }

            $nestedSetEntity
                ->setLft($lft)
                ->setLvl($lvl)
                ->setTree($tree)
                ->setRgt($rgt)
                ->setParentId($parentId);

            $this->getEntityManager()->persist($nestedSetEntity);
            $this->getEntityManager()->flush();

            $this->getEntityManager()->commit();


        } catch (\Throwable $e) {
            $this->getEntityManager()->rollback();
            throw $e;
        }

        return $nestedSetEntity;
    }

    public function up(NestedSetEntityInterface $node): void
    {
        try {
            $this->changeNodeKeys($node, NestedSetServiceRepositoryInterface::NODE_BEFORE);
        } catch (ElementNotFoundException $exception) {
            $this->changeNodeKeys($node, NestedSetServiceRepositoryInterface::NODE_LAST);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    public function down(NestedSetEntityInterface $node): void
    {
        try {
            $this->changeNodeKeys($node, NestedSetServiceRepositoryInterface::NODE_AFTER);
        } catch (ElementNotFoundException $exception) {
            $this->changeNodeKeys($node, NestedSetServiceRepositoryInterface::NODE_FIRST);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    public function delete(
        NestedSetEntityInterface $nestedSetEntity,
        ?string $tableName = null,
        ?Connection $connection = null
    ): void {
        if (empty($connection)) {
            $connection = $this->getEntityManager()->getConnection();
        }

        if (empty($tableName)) {
            $tableName = $this->getTableName();
        }

        $sql = $this->getDeleteQuery($nestedSetEntity, $tableName);

        $connection->executeQuery($sql);
    }

    public function move(NestedSetEntityInterface $node, ?NestedSetEntityInterface $parent = null): void
    {
        try {
            (new NestedSetsMoveNode($this->getEntityManager(), $this))
                ->move($node, $parent);
        } catch (ElementNotFoundException | \Throwable $exception) {
            throw $exception;
        }
    }

    public function getDeleteQuery (
        NestedSetEntityInterface $nestedSetEntity,
        string $tableName
    ): string {

        $sql = "DELETE FROM `{$tableName}` 
                    WHERE lft >= {$nestedSetEntity->getLft()} 
                        AND rgt <= {$nestedSetEntity->getRgt()} 
                        AND tree = {$nestedSetEntity->getTree()};";

        $sql .= "UPDATE `{$tableName}` SET
                    lft = IF (lft > {$nestedSetEntity->getLft()},
                    lft - (((( {$nestedSetEntity->getRgt()} - {$nestedSetEntity->getLft()} - 1) / 2) + 1)*2), lft),
                    rgt = rgt- (((( {$nestedSetEntity->getRgt()} - {$nestedSetEntity->getLft()} - 1) / 2) + 1)*2)

                 WHERE rgt > {$nestedSetEntity->getRgt()} AND tree = {$nestedSetEntity->getTree()};";

        return $sql;
    }

    private function getNearNode(NestedSetEntityInterface $node, int $direction): ?NestedSetEntityInterface
    {
        $queryBuilder = $this->getItemQueryBuilder();

        switch ($direction)
        {
            case self::NODE_AFTER;
                $queryBuilder->andWhere("{$this->alias}.lft>:lft")->setParameter("lft", $node->getLft());
                $queryBuilder->orderBy("{$this->alias}.lft", "ASC");
                break;

            case self::NODE_FIRST;
                $queryBuilder->andWhere("{$this->alias}.lft=:lft")->setParameter("lft", 1);
                break;

            case self::NODE_LAST;
                $queryBuilder->orderBy("{$this->alias}.lvl", "DESC");
                break;

            case self::NODE_BEFORE:
                $queryBuilder->andWhere("{$this->alias}.rgt>:rgt")->setParameter("rgt", $node->getRgt());
                $queryBuilder->orderBy("{$this->alias}.lft", "DESC");
                break;
        }

        $queryBuilder->andWhere("{$this->alias}.tree=:tree")->setParameter("tree", $node->getTree());
        $queryBuilder->setFirstResult(0)->setMaxResults(1);
        $return = $queryBuilder->getQuery()->getOneOrNullResult();

        if (empty($return)) {
            throw new ElementNotFoundException();
        }

        $this->getEntityManager()->refresh($return);
        return $return;
    }

    private function changeNodeKeys(NestedSetEntityInterface $node, int $direction): void
    {
        try {
            $nearNode = $this->getNearNode($node, $direction);

            if (empty($nearNode)) {
                throw new ElementNotFoundException();
            }

            $lft = $node->getLft();
            $rgt = $node->getRgt();
            $lvl = $node->getLvl();
            $parentId = $node->getParentId();

            $node->setLft($nearNode->getLft())
                ->setRgt($nearNode->getRgt())
                ->setParentId($nearNode->getParentId())
                ->setLvl($nearNode->getLvl());

            $nearNode->setLft($lft)
                ->setRgt($rgt)
                ->setParentId($parentId)
                ->setLvl($lvl);

            $this->getEntityManager()->flush($node);
            $this->getEntityManager()->flush($nearNode);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }
}
