<?php

namespace MartenaSoft\Common\Event;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractCommonFormEvent extends Event implements
    CommonFormEventInterface,
    CommonEventResponseInterface,
    CommonFormEventEntityInterface
{
    private FormInterface $form;
    private ?Response $response = null;
    private CommonEntityInterface $entity;

    public function __construct(FormInterface $form, CommonEntityInterface $entity)
    {
        $this->form = $form;
        $this->entity = $entity;
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

    public function getEntity(): CommonEntityInterface
    {
        return $this->entity;
    }
}
