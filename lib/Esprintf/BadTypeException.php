<?php

declare(strict_types=1);

namespace Esprintf;

class BadTypeException extends EsprintfException
{
    const BAD_CSS_TYPE       = "Can't use type %s as CSS. Only one of string, stringable, CssEscapedString would be acceptable.";
    const BAD_HTML_TYPE      = "Can't use type %s as HTML. Only one of string, stringable, HtmlEscapedString would be acceptable.";
    const BAD_HTML_ATTR_TYPE = "Can't use type %s as HTML attribute. Only one of string, stringable, HtmlAttrEscapedString would be acceptable.";
    const BAD_JS_TYPE        = "Can't use type %s as JavaScript. Only one of string, stringable, JsEscapedString would be acceptable.";
    const BAD_URL_TYPE       = "Can't use type %s as URL. Only one of string, stringable, UrlEscapedString would be acceptable.";

    public static function badCss($value)
    {
        $message = sprintf(
            self::BAD_CSS_TYPE,
            get_debug_type($value)
        );

        return new self($message);
    }

    public static function badHtml(mixed $value)
    {
        $message = sprintf(
            self::BAD_HTML_TYPE,
            get_debug_type($value)
        );

        return new self($message);
    }

    public static function badHtmlAttr(mixed $value)
    {
        $message = sprintf(
            self::BAD_HTML_ATTR_TYPE,
            get_debug_type($value)
        );

        return new self($message);
    }

    public static function badJs(mixed $value)
    {
        $message = sprintf(
            self::BAD_JS_TYPE,
            get_debug_type($value)
        );

        return new self($message);
    }

    public static function badUrl(mixed $value)
    {
        $message = sprintf(
            self::BAD_URL_TYPE,
            get_debug_type($value)
        );

        return new self($message);
    }
}
