<?php

namespace MartenaSoft\Common\Exception;

class CommonException extends \Exception implements CommonExceptionInterface
{
    private ?string $publicMessage = null;

    public function getUserMessage(): ?string
    {
        return $this->publicMessage;
    }

    public function setUserMessage(string $publicMessage): void
    {
        $this->publicMessage = $publicMessage;
    }
}

