<?php

declare(strict_types=1);

namespace EsprintfTest;

use function Esprintf\validateHtmlTemplateString;

/**
 * @coversNothing
 */
class ValidateTest extends BaseTestCase
{
    /**
     * @covers ::\Esprintf\validateHtmlTemplateString
     */
    public function testValidIsOk()
    {
        $result = validateHtmlTemplateString("<div></div>");
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testInvalidHasErrors()
    {
        $this->markTestSkipped();

        // TODO - this test is pretty not great.
        $result = validateHtmlTemplateString("<div><<</div>");

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
}
