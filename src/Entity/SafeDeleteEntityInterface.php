<?php

namespace MartenaSoft\Common\Entity;

interface SafeDeleteEntityInterface
{
    public function isDeleted(): ?bool;
    public function setIsDeleted(?bool $isDeleted): self;
}