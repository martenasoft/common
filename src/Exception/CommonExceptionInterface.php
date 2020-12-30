<?php

namespace MartenaSoft\Common\Exception;

interface CommonExceptionInterface
{
    public function getUserMessage(): ?string;

    public function setUserMessage(string $publicMessage): void;
}