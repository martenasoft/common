<?php

namespace MartenaSoft\Common\Event;

use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class CommonConfirmAfterSubmitEvent extends Event implements CommonFormEventInterface
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

    public static function getEventName(): string
    {
        return 'common.config.after.submit.event';
    }
}