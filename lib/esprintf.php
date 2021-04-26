<?php

declare(strict_types=1);

namespace Esprintf {

    function rawString(string $string)
    {
        return $string;
    }

    /**
     * @param string $search The string that will be searched for.
     * @return callable The callable that will do the escaping for the replacement.
     * @throws EsprintfException
     */
    function getEscapeCallable($search)
    {
        static $escaper = null;
        if ($escaper === null) {
            $escaper = new \Laminas\Escaper\Escaper('utf-8');
        }

        $callables = [
            ':attr_' => [$escaper, 'escapeHtmlAttr'],
            ':js_' => [$escaper, 'escapeJs'],
            ':css_' => [$escaper, 'escapeCss'],
            ':uri_' => [$escaper, 'escapeUrl'],
            ':raw_' => 'Esprintf\rawString',
            ':html_' => [$escaper, 'escapeHtml']
        ];

        foreach ($callables as $key => $callable) {
            if (strpos($search, $key) === 0) {
                return $callable;
            }
        }

        throw EsprintfException::fromUnknownSearchString($search);
    }

    function validateHtmlTemplateString(string $string): array
    {
        libxml_use_internal_errors(true);

        $dom = new \DomDocument();
        $dom->validateOnParse = true;
        $dom->loadHTML('<?xml encoding="UTF-8">' . $string);

        return libxml_get_errors();
    }
}

namespace {

    use Esprintf\EscapedString;
    use Esprintf\EsprintfException;
    use Esprintf\UnsafeTemplateException;

    /**
     * @param string|EscapedString $template
     * @param array<string, string> $searchReplace
     * @return string
     * @throws EsprintfException
     */
    function esprintf(string|EscapedString $template, array $searchReplace): EscapedString
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
            $escapeFn = Esprintf\getEscapeCallable($search);
            $escapedParams[$search] = $escapeFn($replace);
        }

        $stringResult = str_replace(
            array_keys($escapedParams),
            $escapedParams,
            $template
        );

        return EscapedString::fromString($stringResult);
    }
}
