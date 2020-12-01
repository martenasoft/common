<?php

namespace MartenaSoft\Common\Repository;

use MartenaSoft\Trash\Entity\TrashEntityInterface;

interface SafeRepositoryDeleteInterface
{
    public const SHOW_ACTIVE = 1;
    public const SHOW_ALL = 2;
    public const SHOW_DELETED = 3;

    public function safeDelete(TrashEntityInterface $entity): void;
    public function getHowToShowSafeDeletedItems(): int;
    public function setHowToShowSafeDeletedItems(int $showStatus): void;
}
