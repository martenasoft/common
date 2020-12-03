<?php

namespace MartenaSoft\Common\Event;

use Symfony\Component\HttpFoundation\Response;

interface CommonEventResponseInterface
{
    public function getResponse(): ?Response;

    public function setResponse(?Response $response): self;

    public function getRedirectUrl(): ?string;

    public function setRedirectUrl(string $redirectUrl): self;
}
