<?php

namespace MartenaSoft\Common\Event;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use MartenaSoft\Common\Event\InitEventsTraits\InitEventResponse;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class CommonConfirmAfterSubmitEvent extends AbstractCommonConfirmAfterSubmitEvent
{
    public static function getEventName(): string
    {
        return 'common.config.after.submit.event';
    }
}