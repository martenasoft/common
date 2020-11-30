<?php

namespace MartenaSoft\Common\Service\ClassNamePrefixService;

class ClassNamePrefixService implements ClassNamePrefixServiceInterface
{
    private string $className;

    public function setClassName(string $className): self
    {
        $this->className = $className;
        return $this;
    }

    public function getEntityClassName(?string  $className = null): string
    {
        $class = new \ReflectionClass($this->getClassNameFormArgument($className));
        $className = $class->getShortName();
        preg_match('/[A-Z]{1}[a-z]+/',  $className, $arr);
        return (!empty($arr[0]) ? $arr[0] : $className);
    }

    private function getClassNameFormArgument(?string $className): string
    {
        if (empty($className)) {
            $className = $this->className;
        }
        return $className;
    }
}
