<?php

namespace MartenaSoft\Common\Event;

class CommonFormAfterSaveEvent extends AbstractCommonFormEvent
{
    public static function getEventName(): string
    {
        return 'common.form.after.save';
    }
}
