<?php

namespace MartenaSoft\Common\Event;

interface LoadConfigEventInterface
{
    public function getEntityClassName(): string;
}