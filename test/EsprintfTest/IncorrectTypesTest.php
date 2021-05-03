<?php

declare(strict_types=1);

namespace EsprintfTest;

use Esprintf\EsprintfException;
use Esprintf\HtmlEscapedString;
use function Esprintf\cssEscape;
use function Esprintf\htmlEscape;
use function Esprintf\htmlAttrEscape;
use function Esprintf\jsEscape;
use function Esprintf\urlEscape;
use function Danack\PHPUnitHelper\templateStringToRegExp;

class IncorrectTypesTest extends BaseTestCase
{
    public function testBasic()
    {
        $result = urlEscape("http://www.google.com/");

        var_dump($result);
        exit(0);
    }


    public function testIncorrectUrlUsage()
    {
        $result = urlEscape("http://www.google.com/");

        $template = <<< HTML
  <div class=":attr_class">
  
    <button onclick=":js_onclick" style=":css_style"></button>
  
    <a href=":url_link" >
      Clicky link - :html_description
    </a> 
  </div>

HTML;



    }

}
