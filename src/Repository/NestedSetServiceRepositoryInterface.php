<?php

namespace MartenaSoft\Common\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use MartenaSoft\Common\Entity\NestedSetEntityInterface;

interface NestedSetServiceRepositoryInterface
{
    public function create(NestedSetEntityInterface $nestedSetEntity, ?NestedSetEntityInterface $parent = null);

    public function delete(
        NestedSetEntityInterface $nestedSetEntity,
        ?string $tableName = null,
        ?Connection $connection = null
    ): void;

    public function move(NestedSetEntityInterface $node, ?NestedSetEntityInterface $parent = null): void;

    public function getTableName(): string;

    public function getMaxTree(): int;
}
