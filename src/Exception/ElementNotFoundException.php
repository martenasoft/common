<?php

namespace MartenaSoft\Common\Exception;

use Throwable;

class ElementNotFoundException extends CommonException
{
    public const MESSAGE = 'Item %s not found';
    public const CODE = 1;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = sprintf(self::MESSAGE, $message);

        if (empty($code)) {
            $code = self::CODE;
        }

        parent::__construct($message, $code, $previous);
    }
}