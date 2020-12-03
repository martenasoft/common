<?php

namespace MartenaSoft\Common\Event;

use Symfony\Component\Form\FormInterface;

interface CommonFormEventInterface
{
    public function getForm(): FormInterface;
}
