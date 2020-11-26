<?php

namespace MartenaSoft\Common\Event;


class CommonFormAfterDeleteEvent extends AbstractCommonDeleteItemEvent
{
    public static function getEventName(): string
    {
        return 'common.form.after.delete';
    }
}