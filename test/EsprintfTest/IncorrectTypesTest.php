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
        yield ['<div>:html_description</div>', ":html_description", BadTypeException::BAD_HTML_TYPE];
        yield ['<div style=":css_foo">blah</div>', ":css_foo", BadTypeException::BAD_CSS_TYPE];
        yield ['<div onclick=":js_onclick">blah</div>', ":js_onclick", BadTypeException::BAD_JS_TYPE];
        yield ['<a href="http://www.google.com?foo=:uri_foo">blah</a>', ":uri_foo", BadTypeException::BAD_JS_TYPE];
    }

    /**
     * @group wip
     * @dataProvider providesBasicErrorsHtmlAttr
     */
    public function testBasicErrorsHtmlAttr($html_template, $search_key, $expectedMessage)
    {
        $attrEscaped = htmlAttrEscape("john");

        try {
            $result = (string)html_printf($html_template, [$search_key => $attrEscaped]);
        }
        catch (BadTypeException $bte) {
            $this->assertMatchesRegularExpression(
                templateStringToRegExp($expectedMessage),
                $bte->getMessage()
            );
            $this->assertStringContainsString(
                HtmlAttrEscapedString::class,
                $bte->getMessage()
            );
        }
    }

}
