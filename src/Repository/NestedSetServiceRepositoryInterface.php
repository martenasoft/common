<?php

namespace MartenaSoft\Common\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use MartenaSoft\Common\Entity\NestedSetEntityInterface;

interface NestedSetServiceRepositoryInterface
{
    public const NODE_BEFORE = 1;
    public const NODE_AFTER = 2;
    public const NODE_FIRST = 3;
    public const NODE_LAST = 4;

    public function create(NestedSetEntityInterface $nestedSetEntity, ?NestedSetEntityInterface $parent = null);

    public function delete(
        NestedSetEntityInterface $nestedSetEntity,
        ?string $tableName = null,
        ?Connection $connection = null
    ): void;

    public function move(NestedSetEntityInterface $node, ?NestedSetEntityInterface $parent = null): void;

    public function getTableName(): string;

    public function getMaxTree(): int;

    public function getDeleteQuery (
        NestedSetEntityInterface $nestedSetEntity,
        string $tableName
    ): string;

    public function up(NestedSetEntityInterface $node): void;

    public function down(NestedSetEntityInterface $node): void;
}
