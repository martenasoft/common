<?php

namespace MartenaSoft\Common\Service\ClassNamePrefixService;

interface ClassNamePrefixServiceInterface
{
    public function setClassName(string $className
    ): \MartenaSoft\Common\Service\ClassNamePrefixService\ClassNamePrefixService;

    public function getEntityClassName(?string $className = null): string;
}