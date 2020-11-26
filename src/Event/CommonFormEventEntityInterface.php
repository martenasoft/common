<?php

namespace MartenaSoft\Common\Event;

use MartenaSoft\Common\Entity\CommonEntityInterface;

interface CommonFormEventEntityInterface
{
    public function getEntity(): CommonEntityInterface;
}