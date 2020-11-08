<?php

namespace MartenaSoft\Common\Entity;

class ConfirmDeleteEntity
{
    private bool $isSafeDelete = false;

    public function isSafeDelete(): ?bool
    {
        return $this->isSafeDelete;
    }

    public function setIsSafeDelete(?bool $isSafeDelete): self
    {
        $this->isSafeDelete = $isSafeDelete;
        return $this;
    }
}

