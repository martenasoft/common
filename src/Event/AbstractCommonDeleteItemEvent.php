<?php

namespace MartenaSoft\Common\Event;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use MartenaSoft\Trash\Entity\InitTrashMethodsTrait;
use MartenaSoft\Common\Event\InitEventsTraits\InitEventResponse;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractCommonDeleteItemEvent extends Event implements
    CommonFormEventEntityInterface,
    CommonEventInterface,
    CommonEventResponseInterface
{
    use
        InitTrashMethodsTrait,
        InitEventResponse
        ;

    private CommonEntityInterface $entity;
    private bool $isSafeDelete = false;


    public function __construct(CommonEntityInterface $entity, bool $isSafeDelete)
    {
        $this->entity = $entity;
    }

    public function getEntity(): CommonEntityInterface
    {
        return $this->entity;
    }

}
