<?php

namespace MartenaSoft\Common\Event;

use Symfony\Contracts\EventDispatcher\Event;

class LoadConfigEvent extends Event implements CommonEventInterface, LoadConfigEventInterface
{
    private string $entityClassName;

    public function __construct(string $entityClassName)
    {
        $this->entityClassName = $entityClassName;
    }

    public static function getEventName(): string
    {
        return 'common.load.config.event';
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }
}
