<?php

declare(strict_types=1);

namespace EsprintfTest;

use function Esprintf\validateHtmlTemplateString;

class ValidateTest extends BaseTestCase
{
    public function testValidIsOk()
    {
        $result = validateHtmlTemplateString("<div></div>");
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testInvalidHasErrors()
    {
        // TODO - this test is pretty not great.
        $result = validateHtmlTemplateString("<div><<</div>");

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

}
