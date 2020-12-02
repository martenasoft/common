<?php

namespace MartenaSoft\Common\Event;

use Symfony\Component\Form\FormInterface;

interface CommonFormEventInterface extends CommonEventInterface
{
    public function getForm(): FormInterface;
}
