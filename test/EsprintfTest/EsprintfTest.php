<?php

declare(strict_types=1);

namespace EsprintfTest;

use Esprintf\EsprintfException;
use Esprintf\EscapedString;
use function Danack\PHPUnitHelper\templateStringToRegExp;

class EsprintfTest extends BaseTestCase
{
    public function testRaw()
    {
        $string = 'foo :raw_text bar';

        $params = [
            ':raw_text' => 'foo bar'
        ];

        $result = esprintf($string, $params);
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

        esprintf($string, $params);
    }

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

        $result = esprintf($string, $params);
        $this->assertEquals('foo foo bar bar', $result);
    }


    function testSafeTemplateDoesntThrowAndReturnsEscapedString()
    {
        $templateString = '<span class=":attr_class">:html_username</span>';
        $result = esprintf($templateString, [':attr_class' => 'red']);
        $this->assertInstanceOf(EscapedString::class, $result);

        $stringResult = $result->__toString();
        $this->assertSame(
            '<span class="red">:html_username</span>',
            $stringResult
        );
    }

    function testEscapedStringDoesntThrow()
    {
        $escapedString = EscapedString::fromString(
            '<span class="red">:html_username</span>',
        );
        $result = esprintf($escapedString, [':html_username' => 'danack']);

        $this->assertInstanceOf(EscapedString::class, $result);

        $stringResult = $result->__toString();
        $this->assertSame(
            '<span class="red">danack</span>',
            $stringResult
        );
    }

    function testUnsafeTemplateThrowsException()
    {
        $this->expectException(\Esprintf\UnsafeTemplateException::class);

        $this->expectErrorMessageMatches(
            templateStringToRegExp(\Esprintf\UnsafeTemplateException::UNKNOWN_ESCAPER_STRING)
        );

        $generatedString = 'foobar' . strlen('foobar');
        esprintf($generatedString, []);
    }
}
