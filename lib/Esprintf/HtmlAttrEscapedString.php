<?php

declare(strict_types=1);

namespace Esprintf;

// Escape a string for the HTML Attribute context.
class HtmlAttrEscapedString implements EscapedString
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
