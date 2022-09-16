<?php

declare(strict_types=1);

namespace Esprintf;

class UnsafeTemplateException extends EsprintfException
{
    const UNKNOWN_ESCAPER_STRING = "Unsafe template string. Template must either be hardcoded string or from previously escaped string.";

    public static function blah()
    {
        return new self(self::UNKNOWN_ESCAPER_STRING);
    }
}
