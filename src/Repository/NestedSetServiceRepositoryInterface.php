<?php

namespace MartenaSoft\Common\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use MartenaSoft\NestedSets\Entity\NodeInterface;

interface NestedSetServiceRepositoryInterface
{
    public const NODE_BEFORE = 1;
    public const NODE_AFTER = 2;
    public const NODE_FIRST = 3;
    public const NODE_LAST = 4;

    public function create(NodeInterface $nestedSetEntity, ?NodeInterface $parent = null);

    public function delete(
        NodeInterface $nestedSetEntity,
        ?string $tableName = null,
        ?Connection $connection = null
    ): void;

    public function move(NodeInterface $node, ?NodeInterface $parent = null): void;

    public function getTableName(): string;

    public function getMaxTree(): int;

    public function getDeleteQuery (
        NodeInterface $nestedSetEntity,
        string $tableName
    ): string;

    public function up(NodeInterface $node): void;

    public function down(NodeInterface $node): void;
}
