<?php

namespace MartenaSoft\Common\Service\DirectoryManagerService;

use MartenaSoft\Common\Exception\CommonException;
use Symfony\Component\HttpKernel\KernelInterface;

class DirectoryManagerService
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function createDirByPath(string $path, ?string $rootDir = null): bool
    {
        if ($rootDir === null) {
            $rootDir = $this->kernel->getProjectDir();
        }

        if ($this->isWritable($path, $rootDir,false)) {
            return true;
        }

        $pathArray = explode(DIRECTORY_SEPARATOR, $this->cleanPath($rootDir . DIRECTORY_SEPARATOR . $path));
        $path_ = "";

        foreach ($pathArray as $dir) {
            if (!empty($dir)) {
                $path_ .= $this->cleanPath(DIRECTORY_SEPARATOR . $dir);
                if (!is_dir($path_)) {
                    mkdir($path_, 0777);
                }
            }
        }
    }

    public function isWritable(string $path, string $rootDir, bool $isThrowErrors = true): bool
    {
        $error = null;
        $message = null;
        $path_ = $this->cleanPath($rootDir . DIRECTORY_SEPARATOR . $path);

        if (!is_dir($path_)) {
            $message = "Directory %s not found";
        } elseif (!is_readable($path_)) {
            $message = "Directory %s is not readable";
        } elseif (!is_writeable($path_)) {
            $message = "Directory %s is not writeable";
        } else {

            return true;
        }

        if (!empty($message) && $isThrowErrors) {
            $error = new CommonException(sprintf($message, $path_));
            $error->setUserMessage(sprintf($message, $path));
            throw $error;
        }

        return false;
    }

    public function cleanPath(string $path): string
    {
        return preg_replace(['/(\/{2,})/', '/(\/{1,})$/'], ['/', ''], $path);
    }
}
