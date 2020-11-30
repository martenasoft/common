<?php

namespace MartenaSoft\Common\Service\ConfigService;

use MartenaSoft\Common\Entity\CommonEntityConfigInterface;

interface CommonConfigServiceInterface
{
    public function get(string $name): array;
    public function isEntity2DefaultValue($isGenerateValueFromEntityIfEmpty): self;
}