<?php

declare(strict_types=1);

namespace EsprintfTest;

use Esprintf\EsprintfException;
use Esprintf\HtmlEscapedString;
use function Danack\PHPUnitHelper\templateStringToRegExp;

/**
 * @coversNothing
 */
class EsprintfTest extends BaseTestCase
{
    /**
     * @covers ::html_printf
     */
    public function testhtml_printf()
    {
        $string = 'foo :html_text bar';

        $params = [
            ':html_text' => 'foo bar'
        ];

        $result = html_printf($string, $params);
        $this->assertEquals('foo foo bar bar', $result);
    }

    public function providesBadKeyGivesException()
    {
        $hasMissingKey = [
            ':html_text1' => 'foo bar',
            ':html_text2' => 'foo bar',
            'foo bar',
        ];

        $hasBadKey = [
            ':html_text1' => 'foo bar',
            ':html_text2' => 'foo bar',
            5 => 'foo bar',
        ];

        return [
            [0, 0, ['foo']],
            [2, 0, $hasMissingKey],
            [2, 5, $hasBadKey],
        ];
    }

    /**
     * @param $searchReplaceArray
     * @dataProvider providesBadKeyGivesException
     * @covers ::html_printf
     */
    public function testBadKeyGivesException($position, $badKey, $params)
    {
        $string = 'Irrelevant to test';
        $this->expectException(EsprintfException::class);

        $expectedMessage = sprintf(
            EsprintfException::KEY_IS_NOT_STRING,
            $position,
            $badKey
        );

        $this->expectExceptionMessage($expectedMessage);

        html_printf($string, $params);
    }

    /**
     * @covers ::html_printf
     */
    function testUnknownEscaper()
    {
        $string = 'Irrelevant to test';

        $params = [
            ':hmtl_text' => 'foo bar' // typo in key
        ];

        $this->expectException(EsprintfException::class);

        $expectedMessage = sprintf(
            EsprintfException::UNKNOWN_ESCAPER_STRING,
            ':hmtl_text'
        );

        $this->expectExceptionMessage($expectedMessage);

        $result = html_printf($string, $params);
        $this->assertEquals('foo foo bar bar', $result);
    }

    /**
     * @covers ::html_printf
     */
    function testSafeTemplateDoesntThrowAndReturnsEscapedString()
    {
        $templateString = '<span class=":attr_class">:html_username</span>';
        $result = html_printf($templateString, [':attr_class' => 'red']);
        $this->assertInstanceOf(HtmlEscapedString::class, $result);

        $stringResult = $result->__toString();
        $this->assertSame(
            '<span class="red">:html_username</span>',
            $stringResult
        );
    }

    /**
     * @covers ::html_printf
     */
    function testEscapedStringDoesntThrow()
    {
        $escapedString = HtmlEscapedString::fromString(
            '<span class="red">:html_username</span>',
        );
        $result = html_printf($escapedString, [':html_username' => 'danack']);

        $this->assertInstanceOf(HtmlEscapedString::class, $result);

        $stringResult = $result->__toString();
        $this->assertSame(
            '<span class="red">danack</span>',
            $stringResult
        );
    }

    // This was dependent on is_literal
//    function testUnsafeTemplateThrowsException()
//    {
//        $this->expectException(\Esprintf\UnsafeTemplateException::class);
//
//        $this->expectErrorMessageMatches(
//            templateStringToRegExp(\Esprintf\UnsafeTemplateException::UNKNOWN_ESCAPER_STRING)
//        );
//
//        $generatedString = 'foobar' . strlen('foobar');
//        html_printf($generatedString, []);
//    }
}
