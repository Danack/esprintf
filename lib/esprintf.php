<?php

declare(strict_types=1);

namespace Esprintf {

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

    function htmlAttrEscape(string $input): JsEscapedString
    {
        $escaper = new \Laminas\Escaper\Escaper('utf-8');
        $result = $escaper->escapeJs($input);
        return JsEscapedString::fromString($result);
    }

    function jsEscape(string $input): JsEscapedString
    {
        $escaper = new \Laminas\Escaper\Escaper('utf-8');
        $result = $escaper->escapeJs($input);
        return JsEscapedString::fromString($result);
    }

    function urlEscape(string $x): UrlEscapedString
    {
        $escaper = new \Laminas\Escaper\Escaper('utf-8');
        $result = $escaper->escapeUrl($x);
        return UrlEscapedString::fromString($result);
    }

    function escape_input($search, $replace): EscapedString
    {
        if (strpos($search, ':html_' ) === 0) {
            if ($replace instanceof HtmlEscapedString) {
                return $replace;
            }

            // Some other type of already escaped string
            if ($replace instanceof EscapedString) {
                throw BadTypeException::badHtml($search);
            }

            return htmlEscape($replace);
        }

        if (strpos($search, ':attr_') === 0) {
            if ($replace instanceof HtmlAttrEscapedString) {
                return $replace;
            }
            // Some other type of already escaped string
            if ($replace instanceof EscapedString) {
                throw BadTypeException::badHtmlAttr($search);
            }

            return htmlAttrEscape($replace);
        }
        if (strpos($search, ':js_') === 0) {
            if ($replace instanceof JsEscapedString) {
                return $replace;
            }

            // Some other type of already escaped string
            if ($replace instanceof EscapedString) {
                throw BadTypeException::badJs($search);
            }

            return jsEscape($replace);
        }
        if (strpos($search, ':css_') === 0) {
            if ($replace instanceof CssEscapedString) {
                return $replace;
            }
            // Some other type of already escaped string
            if ($replace instanceof EscapedString) {
                throw BadTypeException::badCss($search);
            }

            return cssEscape($replace);
        }
        if (strpos($search, ':uri_') === 0) {
            if ($replace instanceof UrlEscapedString) {
                return $replace;
            }

            // Some other type of already escaped string
            if ($replace instanceof EscapedString) {
                throw BadTypeException::badUrl($search);
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
    }


//    /**
//     * @param string $search The string that will be searched for.
//     * @return callable The callable that will do the escaping for the replacement.
//     * @throws EsprintfException
//     */
//    function getEscapeCallable($search)
//    {
//        static $escaper = null;
//        static $callables = null;
//        if ($escaper === null) {
//            $escaper = new \Laminas\Escaper\Escaper('utf-8');
//
//            $callables = [
//                ':attr_' => [$escaper, 'escapeHtmlAttr'],
//                ':js_' => [$escaper, 'escapeJs'],
//                ':css_' => [$escaper, 'escapeCss'],
//                ':uri_' => [$escaper, 'escapeUrl'],
//    //            ':raw_' => 'Esprintf\rawString',
//                ':html_' => [$escaper, 'escapeHtml']
//            ];
//        }
//
//        foreach ($callables as $key => $callable) {
//            if (strpos($search, $key) === 0) {
//                return $callable;
//            }
//        }
//
//        throw EsprintfException::fromUnknownSearchString($search);
//    }
}

namespace {

    use Esprintf\EscapedString;
    use Esprintf\HtmlEscapedString;
    use Esprintf\EsprintfException;
    use Esprintf\UnsafeTemplateException;

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
            if (is_literal($template) !== true) {
                throw UnsafeTemplateException::blah();
            }
        }
        // Not a string, but an EscapedString object
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
