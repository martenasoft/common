<?php

namespace MartenaSoft\Common\Event;

class CommonFormShowEvent extends AbstractCommonFormEvent
{
    public static function getEventName(): string
    {
        return 'common.form.show.event';
    }
}

