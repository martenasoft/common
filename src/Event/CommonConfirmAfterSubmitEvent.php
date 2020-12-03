<?php

namespace MartenaSoft\Common\Event;

class CommonConfirmAfterSubmitEvent extends AbstractCommonConfirmAfterSubmitEvent
{
    public static function getEventName(): string
    {
        return 'common.config.after.submit.event';
    }
}