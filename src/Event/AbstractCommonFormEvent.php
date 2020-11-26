<?php

namespace MartenaSoft\Common\Event;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

abstract class AbstractCommonFormEvent extends Form implements CommonFormEventInterface
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
