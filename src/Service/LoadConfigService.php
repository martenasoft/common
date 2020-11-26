<?php

namespace MartenaSoft\Common\Service;

use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\Common\Entity\CommonEntityInterface;

class LoadConfigService implements LoadConfigServiceInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getConfigByUrl(string $url, string $entityClassName): ?CommonEntityInterface
    {
        $this->entityManager->findByUrl($url);
    }
}
