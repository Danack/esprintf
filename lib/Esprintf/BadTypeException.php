<?php

declare(strict_types=1);


namespace Esprintf;


class BadTypeException extends EsprintfException
{

    const BAD_CSS_TYPE          = "blah blah, can't use type %s where string|stringable|CssEscapedString";
    const BAD_HTML_TYPE         = "blah blah, can't use type %s where string|stringable|HtmlEscapedString";
    const BAD_HTML_ATTR_TYPE    = "blah blah, can't use type %s where string|stringable|HtmlAttrEscapedString";
    const BAD_JS_TYPE           = "blah blah, can't use type %s where string|stringable|JsEscapedString";
    const BAD_URL_TYPE          = "blah blah, can't use type %s where string|stringable|UrlEscapedString";


    public static function badCss($value)
    {
        $message = sprintf(
            self::BAD_CSS_TYPE,
            get_debug_type($value)
        );

        return new self($message);
    }

}