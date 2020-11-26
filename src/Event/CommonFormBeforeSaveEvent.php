<?php

namespace MartenaSoft\Common\Event;

class CommonFormBeforeSaveEvent extends AbstractCommonFormEvent
{
    public static function getEventName(): string
    {
        return 'common.form.before.save';
    }
}
