<?php

namespace MartenaSoft\Common\Entity;

interface CommonEntityConfigInterface
{
    public const DEFAULT_NAME = 'default';
    public function getDefaultName(): string;
}
