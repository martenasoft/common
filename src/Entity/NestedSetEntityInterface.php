<?php

namespace MartenaSoft\Common\Entity;

interface NestedSetEntityInterface
{
    public function getId(): ?int;

    public function getLft(): ?int;
    public function setLft(?int $lft): ?self;

    public function getRgt(): ?int;
    public function setRgt(?int $lft): ?self;

    public function getTree(): ?int;
    public function setTree(?int $lft): ?self;

    public function getLvl(): ?int;
    public function setLvl(?int $lft): ?self;

    public function getParentId(): ?int;
}