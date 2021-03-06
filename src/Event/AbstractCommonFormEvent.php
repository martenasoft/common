<?php

namespace MartenaSoft\Common\Event;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use MartenaSoft\Common\Event\InitEventsTraits\InitEventResponse;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractCommonFormEvent extends Event implements
    CommonFormEventInterface,
    CommonEventResponseInterface,
    CommonFormEventEntityInterface
{
    use InitEventResponse;

    private FormInterface $form;
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

    public function getEntity(): CommonEntityInterface
    {
        return $this->entity;
    }
}
