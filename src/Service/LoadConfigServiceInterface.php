<?php

namespace MartenaSoft\Common\Service;

use MartenaSoft\Common\Entity\CommonEntityInterface;

interface LoadConfigServiceInterface
{
    public function getConfigByUrl(string $url, string $entityClassName): ?CommonEntityInterface;
}
