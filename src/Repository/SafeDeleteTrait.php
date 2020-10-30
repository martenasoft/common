<?php

namespace MartenaSoft\Common\Repository;

use Doctrine\ORM\QueryBuilder;
use MartenaSoft\Common\Entity\SafeDeleteEntityInterface;

trait SafeDeleteTrait
{
    private int $howToShowSafeDeletedItems = SafeRepositoryDeleteInterface::SHOW_ACTIVE;

    public function safeDelete(SafeDeleteEntityInterface $entity): void
    {
        if ($entity->isDeleted()) {
            return;
        }
        $entity->setIsDeleted(true);
    }

    public function getHowToShowSafeDeletedItems(): int
    {
        return $this->howToShowSafeDeletedItems;
    }

    public function setHowToShowSafeDeletedItems(int $showStatus): void
    {
        $this->howToShowSafeDeletedItems = $showStatus;
    }

    protected function safeDeleteQueryBuilder(QueryBuilder $queryBuilder): void
    {
        if ($this->getHowToShowSafeDeletedItems() == SafeRepositoryDeleteInterface::SHOW_ALL) {
            return;
        }

        $queryBuilder->andWhere("{$this->alias}.isDeleted=:deletedStatus");
        $queryBuilder->setParameter("deletedStatus", (
            $this->getHowToShowSafeDeletedItems() == SafeRepositoryDeleteInterface::SHOW_DELETED
        ));
    }
}
