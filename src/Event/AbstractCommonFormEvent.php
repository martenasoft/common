<?php

namespace MartenaSoft\Common\Event;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractCommonFormEvent extends Event implements CommonFormEventInterface, CommonEventResponseInterface
{
    private FormInterface $form;
    private ?Response $response = null;

    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): self
    {
        $this->response = $response;
    }
}
