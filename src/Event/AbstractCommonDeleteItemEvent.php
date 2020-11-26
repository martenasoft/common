<?php

namespace MartenaSoft\Common\Event;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractCommonDeleteItemEvent extends Event
    implements CommonFormEventEntityInterface, CommonEventInterface
{
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
