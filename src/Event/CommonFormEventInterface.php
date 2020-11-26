<?php

namespace MartenaSoft\Common\Event;

use Symfony\Component\Form\FormInterface;

interface CommonFormEventInterface extends CommonEventInterface
{
    public function __construct(FormInterface $form);
    public function getForm(): FormInterface;

}
