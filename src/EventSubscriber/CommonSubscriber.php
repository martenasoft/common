<?php

namespace MartenaSoft\Common\EventSubscriber;


use MartenaSoft\Common\Event\LoadConfigEvent;
use MartenaSoft\Common\Event\LoadConfigEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

class CommonSubscriber implements EventSubscriberInterface
{
    public const ON_LOAD_CONFIG_BY_URL_NAME = 'onLoadConfigByUrl';
    private static array $config = [];

    public static function getSubscribedEvents()
    {
        return [
            LoadConfigEvent::getEventName() => [
                [self::ON_LOAD_CONFIG_BY_URL_NAME, 0]
            ],
        ];
    }

    public function onLoadConfigByUrl(LoadConfigEventInterface $event): void
    {
        self::$config[$_SERVER['REQUEST_URI']] = $event->getEntityClassName();
        //dump($event->getEntityClassName(), $_SERVER['REQUEST_URI']); die;
    }

    public static function getConfigs(): array
    {
        return self::$config;
    }
}
