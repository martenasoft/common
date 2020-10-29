<?php

namespace MartenaSoft\Common\Repository;

use Doctrine\Persistence\ManagerRegistry;
use MartenaSoft\Common\Entity\NestedSetEntityInterface;

interface NestedSetServiceRepositoryInterface
{
    public function create(NestedSetEntityInterface $nestedSetEntity, ?NestedSetEntityInterface $parent = null);
    public function safeDelete(NestedSetEntityInterface $nestedSetEntity);
    public function delete(NestedSetEntityInterface $nestedSetEntity);
    public function move(NestedSetEntityInterface $nestedSetEntity, ?NestedSetEntityInterface $parent = null);
}
