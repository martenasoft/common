<?php

namespace MartenaSoft\Common\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;

abstract class AbstractCommonFormEvent extends Event implements CommonFormEventInterface
{
    private FormInterface $form;

    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }
}
