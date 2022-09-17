<?php

declare(strict_types=1);

namespace Esprintf {

    use Esprintf\Esprintf;

    function cssEscape(string $input): CssEscapedString
    {
        $escaper = new \Laminas\Escaper\Escaper('utf-8');
        $result = $escaper->escapeCss($input);
        return CssEscapedString::fromString($result);
    }

    function htmlEscape(string $input): HtmlEscapedString
    {
        $escaper = new \Laminas\Escaper\Escaper('utf-8');
        $result = $escaper->escapeHtml($input);
        return HtmlEscapedString::fromString($result);
    }

    function htmlAttrEscape(string $input): HtmlAttrEscapedString
    {
        $escaper = new \Laminas\Escaper\Escaper('utf-8');
        $result = $escaper->escapeJs($input);
        return HtmlAttrEscapedString::fromString($result);
    }

    function jsEscape(string $input): JsEscapedString
    {
        $escaper = new \Laminas\Escaper\Escaper('utf-8');
        $result = $escaper->escapeJs($input);
        return JsEscapedString::fromString($result);
    }

    function urlEscape(string $x): UrlEscapedString
    {
        static $escaper = null;
        if ($escaper === null) {
            $escaper = new \Laminas\Escaper\Escaper('utf-8');
        }

        $escaper = new \Laminas\Escaper\Escaper('utf-8');
        $result = $escaper->escapeUrl($x);
        return UrlEscapedString::fromString($result);
    }


    function escape_input($search, $replace): EscapedString
    {
        if (str_starts_with($search, Esprintf::HTML) === true) {
            if ($replace instanceof HtmlEscapedString) {
                return $replace;
            }

            // Some other type of already escaped string
            if ($replace instanceof EscapedString) {
                throw BadTypeException::badHtml($replace);
            }

            return htmlEscape($replace);
        }

        if (strpos($search, Esprintf::HTML_ATTR) === 0) {
            if ($replace instanceof HtmlAttrEscapedString) {
                return $replace;
            }
            // Some other type of already escaped string
            if ($replace instanceof EscapedString) {
                throw BadTypeException::badHtmlAttr($replace);
            }

            return htmlAttrEscape($replace);
        }
        if (str_starts_with($search, Esprintf::JS) === true) {
            if ($replace instanceof JsEscapedString) {
                return $replace;
            }

            // Some other type of already escaped string
            if ($replace instanceof EscapedString) {
                throw BadTypeException::badJs($replace);
            }

            return jsEscape($replace);
        }
        if (str_starts_with($search, Esprintf::CSS) === true) {
            if ($replace instanceof CssEscapedString) {
                return $replace;
            }
            // Some other type of already escaped string
            if ($replace instanceof EscapedString) {
                throw BadTypeException::badCss($replace);
            }

            return cssEscape($replace);
        }
        if (str_starts_with($search, Esprintf::URI) === true) {
            if ($replace instanceof UrlEscapedString) {
                return $replace;
            }

            // Some other type of already escaped string
            if ($replace instanceof EscapedString) {
                throw BadTypeException::badUrl($replace);
            }

            return urlEscape($replace);
        }


        throw EsprintfException::fromUnknownSearchString($search);
    }

    function validateHtmlTemplateString(string $string): array
    {
        libxml_use_internal_errors(true);

        $dom = new \DomDocument();
        $dom->validateOnParse = true;
        $dom->loadHTML('<?xml encoding="UTF-8">' . $string);

        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            // handle errors here
        }

        libxml_clear_errors();
        // TODO - check the appropriate handlers are used by parsing the HTML

        return $errors;
    }
}

namespace {

    use Esprintf\EscapedString;
    use Esprintf\HtmlEscapedString;
    use Esprintf\EsprintfException;
//    use Esprintf\UnsafeTemplateException;

    /**
     * @param string|HtmlEscapedString $template
     * @param array<string, string|EscapedString> $searchReplace
     * @return string
     * @throws EsprintfException
     */
    function html_printf(string|HtmlEscapedString $template, array $searchReplace): HtmlEscapedString
    {
        $escapedParams = [];

        if (is_string($template) === true) {
            // TODO - remove this.
//            if (is_literal($template) !== true) {
//                throw UnsafeTemplateException::blah();
//            }
        }
        // Not a string, but an HtmlEscapedString object
        else {
            $template = $template->__toString();
        }

        $count = 0;
        foreach ($searchReplace as $key => $value) {
            if (is_string($key) === false) {
                throw EsprintfException::fromKeyIsNotString($count, $key);
            }
            $count += 1;
        }

        foreach ($searchReplace as $search => $replace) {
            $escapedParams[$search] = Esprintf\escape_input($search, $replace);
        }

        $stringResult = str_replace(
            array_keys($escapedParams),
            $escapedParams,
            $template
        );

        return HtmlEscapedString::fromString($stringResult);
    }
}
