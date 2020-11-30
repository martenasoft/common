<?php

namespace MartenaSoft\Common\Entity;

use MartenaSoft\Content\Entity\ConfigInterface;
use MartenaSoft\Menu\Entity\MenuInterface;

class PageData implements PageDataInterface
{
    private ?MenuInterface $rootNode = null;
    private ?MenuInterface $activeMenu = null;
    private ?ConfigInterface $contentConfig = null;
    private bool $isDetail = false;
    private int $page = 0;
    private string $path = '';

    public function getRootNode(): ?MenuInterface
    {
        return $this->rootNode;
    }

    public function setRootNode(?MenuInterface $rootNode): self
    {
        $this->rootNode = $rootNode;
        return $this;
    }

    public function getActiveMenu(): ?MenuInterface
    {
        return $this->activeMenu;
    }

    public function setActiveMenu(?MenuInterface $activeMenu): self
    {
        $this->activeMenu = $activeMenu;
        return $this;
    }

    public function isDetail(): bool
    {
        return $this->isDetail;
    }

    public function setIsDetail(bool $isDetail): self
    {
        $this->isDetail = $isDetail;
        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getContentConfig(): ?ConfigInterface
    {
        return $this->contentConfig;
    }

    public function setContentConfig(?ConfigInterface $contentConfig): self
    {
        $this->contentConfig = $contentConfig;
        return $this;
    }
}
