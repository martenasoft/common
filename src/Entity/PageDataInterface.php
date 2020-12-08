<?php

namespace MartenaSoft\Common\Entity;

use MartenaSoft\Content\Entity\ConfigInterface;
use MartenaSoft\Menu\Entity\MenuInterface;

interface PageDataInterface
{
    public function getRootNode(): ?MenuInterface;

    public function setRootNode(?MenuInterface $rootNode): self;

    public function getActiveData(): ?CommonEntityInterface;

    public function setActiveData(?CommonEntityInterface $activeData): self;
    
    public function isDetail(): bool;

    public function setIsDetail(bool $isDetail): self;

    public function getPage(): int;

    public function setPage(int $page): self;

    public function getPath(): string;

    public function setPath(string $path): self;

    public function getContentConfig(): ?ConfigInterface;

    public function setContentConfig(?ConfigInterface $contentConfig): self;
}