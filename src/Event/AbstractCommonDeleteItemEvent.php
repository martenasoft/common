<?php

namespace MartenaSoft\Common\Event;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use MartenaSoft\Trash\Entity\InitTrashMethodsTrait;
use MartenaSoft\Trash\Entity\TrashEntityInterface;
//use MartenaSoft\Trash\Event\TrashEventInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractCommonDeleteItemEvent extends Event
    implements CommonFormEventEntityInterface, CommonEventInterface, TrashEntityInterface
{
    use InitTrashMethodsTrait;
    private CommonEntityInterface $entity;

    public function __construct(CommonEntityInterface $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): CommonEntityInterface
    {
        return $this->entity;
    }
}
