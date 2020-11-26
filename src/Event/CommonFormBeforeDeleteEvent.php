<?php

namespace MartenaSoft\Common\Event;


class CommonFormBeforeDeleteEvent extends AbstractCommonDeleteItemEvent
{
    public static function getEventName(): string
    {
        return 'common.form.before.delete';
    }
}