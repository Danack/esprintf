<?php

declare(strict_types=1);

namespace EsprintfTest;

use Esprintf\EsprintfException;
use Esprintf\HtmlAttrEscapedString;
use Esprintf\HtmlEscapedString;
use function Esprintf\cssEscape;
use function Esprintf\htmlEscape;
use function Esprintf\htmlAttrEscape;
use function Esprintf\jsEscape;
use function Esprintf\urlEscape;
use Esprintf\BadTypeException;
use Esprintf\Esprintf;
use function Danack\PHPUnitHelper\templateStringToRegExp;


/**
 * @coversNothing
 */
class IncorrectTypesTest extends BaseTestCase
{
    public function testBasicWorksUri()
    {
        $template = '<a href="http://www.google.com/foo=:uri_name">some description</a>';

        $urlEscaped = urlEscape("john");
        $result = (string)html_printf($template, [":uri_name" => $urlEscaped]);
        $this->assertSame('<a href="http://www.google.com/foo=john">some description</a>', $result);

        $urlEscaped = urlEscape("john&bar=override");
        $result = (string)html_printf($template, [":uri_name" => $urlEscaped]);
        $this->assertSame('<a href="http://www.google.com/foo=john%26bar%3Doverride">some description</a>', $result);
    }


    public function testBasicWorksHtmlAttr()
    {
        $template = '<div data-test=":attr_data">some description</div>';

        $attrEscaped = htmlAttrEscape("john");
        $result = (string)html_printf($template, [":attr_data" => $attrEscaped]);
        $this->assertSame('<div data-test="john">some description</div>', $result);

        $attrEscaped = htmlAttrEscape('john" foo="bar');
        $result = (string)html_printf($template, [":attr_data" => $attrEscaped]);
        $this->assertSame('<div data-test="john\x22\x20foo\x3D\x22bar">some description</div>', $result);
    }


    public function providesBasicErrorsHtmlAttr()
    {
        $types = [
            cssEscape("john"),
            htmlEscape("john"),
            htmlAttrEscape("john"),
            jsEscape("john"),
            urlEscape("john"),
        ];

        $templates = [
            ['<div style=":css_foo">blah</div>', ":css_foo", BadTypeException::BAD_CSS_TYPE],
            ['<div>:html_description</div>', ":html_description", BadTypeException::BAD_HTML_TYPE],
            ['<div align=":attr_foo">blah</div>', ":attr_foo", BadTypeException::BAD_HTML_ATTR_TYPE],
            ['<div onclick=":js_onclick">blah</div>', Esprintf::JS . "onclick", BadTypeException::BAD_JS_TYPE],
            ['<a href="http://www.google.com?foo=:uri_foo">blah</a>', ":uri_foo", BadTypeException::BAD_URL_TYPE],
        ];


        for ($i = 0; $i < count($templates); $i += 1) {
            for ($j = 0; $j < count($types); $j += 1) {
                if ($i === $j) {
                    // Skip providing the acceptable type.
                    continue;
                }

                $data = [
                    $types[$j],
                    $templates[$i][0],
                    $templates[$i][1],
                    $templates[$i][2]
                ];
                yield $data;
            }
        }
    }

    /**
     * @group wip
     * @dataProvider providesBasicErrorsHtmlAttr
     */
    public function testBasicErrorsHtmlAttr($escapedType, $html_template, $search_key, $expectedMessage)
    {
        try {
            $result = (string)html_printf($html_template, [$search_key => $escapedType]);
        }
        catch (BadTypeException $bte) {
            $this->assertMatchesRegularExpression(
                templateStringToRegExp($expectedMessage),
                $bte->getMessage()
            );
            $this->assertStringContainsString(
                get_class($escapedType), //HtmlAttrEscapedString::class,
                $bte->getMessage()
            );
        }
    }

}
