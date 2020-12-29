<?php

namespace MartenaSoft\Common\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use MartenaSoft\Common\Library\CommonStatusInterface;

abstract class AbstractCommonRepository extends ServiceEntityRepository
{
    abstract public static function getAlias(): string;

    public function getQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder !== null ? $queryBuilder : $this->createQueryBuilder(static::getAlias());
    }

   /* protected function getStatus(): int
    {
        return CommonStatusInterface::STATUS_ACTIVE;
    }*/
}
