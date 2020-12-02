<?php

namespace MartenaSoft\Common\Event;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class CommonConfirmAfterSubmitEvent extends Event implements
    CommonFormEventInterface,
    CommonFormEventEntityInterface,
    CommonEventResponseInterface
{
    private FormInterface $form;
    private CommonEntityInterface $entity;
    private ?Response $response = null;

    public function __construct(FormInterface $form, CommonEntityInterface $entity)
    {
        $this->form = $form;
        $this->entity = $entity;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function getEntity(): CommonEntityInterface
    {
        return $this->entity;
    }


    public static function getEventName(): string
    {
        return 'common.config.after.submit.event';
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): self
    {
        $this->response = $response;
        return $this;
    }
}