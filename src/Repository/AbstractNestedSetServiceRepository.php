<?php

namespace MartenaSoft\Common\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use MartenaSoft\Common\Entity\NestedSetEntityInterface;

abstract class AbstractNestedSetServiceRepository extends ServiceEntityRepository
    implements NestedSetServiceRepositoryInterface
{
    public function create(NestedSetEntityInterface $nestedSetEntity, ?NestedSetEntityInterface $parent = null)
    {
        // TODO: Implement create() method.
    }

    public function safeDelete(NestedSetEntityInterface $nestedSetEntity)
    {
        // TODO: Implement safeDelete() method.
    }

    public function delete(NestedSetEntityInterface $nestedSetEntity)
    {
        // TODO: Implement delete() method.
    }

    public function move(NestedSetEntityInterface $nestedSetEntity, ?NestedSetEntityInterface $parent = null)
    {
        // TODO: Implement move() method.
    }
}
