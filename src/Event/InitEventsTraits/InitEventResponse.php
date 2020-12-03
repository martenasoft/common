<?php

namespace MartenaSoft\Common\Event\InitEventsTraits;

use Symfony\Component\HttpFoundation\Response;

trait InitEventResponse
{
    private ?Response $response = null;
    private string $redirectUrl;

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

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(string $redirectUrl): self
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }
}