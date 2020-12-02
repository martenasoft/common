<?php

namespace MartenaSoft\Common\Event;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use MartenaSoft\Trash\Entity\InitTrashMethodsTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractCommonDeleteItemEvent extends Event implements
    CommonFormEventEntityInterface,
    CommonEventInterface,
    CommonEventResponseInterface
{
    use InitTrashMethodsTrait;

    private CommonEntityInterface $entity;
    private bool $isSafeDelete = false;
    private ?Response $response = null;

    public function __construct(CommonEntityInterface $entity, bool $isSafeDelete)
    {
        $this->entity = $entity;
    }

    public function getEntity(): CommonEntityInterface
    {
        return $this->entity;
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

    public function isSafeDelete(): bool
    {
        return $this->isSafeDelete();
    }
}
