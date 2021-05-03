<?php

declare(strict_types=1);

namespace Esprintf;

class CssEscapedString
{
    private string $contents;

    /**
     * @param string $contents
     */
    private function __construct(string $contents)
    {
        $this->contents = $contents;
    }

    public static function fromString(string $contents)
    {
        return new self($contents);
    }

    public function __toString(): string
    {
        return $this->contents;
    }
}
