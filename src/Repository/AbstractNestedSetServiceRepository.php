<?php

namespace MartenaSoft\Common\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use MartenaSoft\Common\Entity\NestedSetEntityInterface;
use MartenaSoft\Common\Entity\SafeDeleteEntityInterface;

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


        if ($nestedSetEntity instanceof SafeDeleteEntityInterface) {
            if ($this->getHowToShowSafeDeletedItems()) {

            }
        }

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
                         WHERE {$this->alias}.tree=:tree AND {$this->alias}.rgt>=:rgt;";

                $sql .= "UPDATE $tableName {$this->alias} 
                            SET {$this->alias}.lft={$this->alias}.lft + 2 
                         WHERE {$this->alias}.tree=:tree AND {$this->alias}.lft>:lft;";

                $params = [
                    'tree' => $tree,
                    'lft' => $lft,
                    'rgt' => $rgt
                ];

                $this->getEntityManager()->getConnection()->executeQuery($sql, $params);
                $lft = $rgt;
                $rgt++;
                $lvl++;
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

    public function delete(
        NestedSetEntityInterface $nestedSetEntity,
        ?string $tableName = null,
        ?Connection $connection = null
    ): void {
        if (empty($tableName)) {
            $tableName = $this->getTableName();
        }

        if (empty($connection)) {
            $connection = $this->getEntityManager()->getConnection();
        }

        $sql = "DELETE FROM `{$tableName}` 
                    WHERE lft >= " . $nestedSetEntity->getLft() . " 
                        AND rgt <= " . $nestedSetEntity->getRgt() . " 
                        AND tree = " . $nestedSetEntity->getTree().";";

        $sql .= "UPDATE `{$tableName}` SET
                    lft = IF (lft > " . $nestedSetEntity->getLft() . ",
                                lft- ((((" . $nestedSetEntity->getRgt() . " - " . $nestedSetEntity->getLft() . " - 1) / 2) + 1)*2), lft),
                    rgt = rgt- ((((" . $nestedSetEntity->getRgt() . " - " . $nestedSetEntity->getLft() . " - 1) / 2) + 1)*2)

                 WHERE rgt > " . $nestedSetEntity->getRgt() . " AND tree = " . $nestedSetEntity->getTree().";";

        $connection->executeQuery($sql);
    }

    public function move(NestedSetEntityInterface $node, ?NestedSetEntityInterface $parent = null): void
    {
        try {
            (new NestedSetsMoveNode($this->getEntityManager(), $this))
                ->move($node, $parent);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }
}
