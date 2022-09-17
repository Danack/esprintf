<?php

require_once __DIR__ . "/vendor/autoload.php";

use function Esprintf\urlEscape;
use function Esprintf\htmlEscape;

//if (function_exists('is_literal') !== true) {
//    echo "This example requires the wip 'is_literal', and can't be run without it.";
//    exit(-1);
//}

// setup representation of user input
$_GET['message'] = file_get_contents(__DIR__ . '/message.txt');

$notification_template = <<< HTML
<span class=":attr_class">
    :html_message
</span>

HTML;

//**********************************************************
// Example of basic use of esprintf library. User input is escaped:
//**********************************************************
echo html_printf(
    $notification_template,
    [':attr_class' => 'alert', ":html_message" => $_GET['message']]
);

//**********************************************************
// Example of partially filling in the template, and passing
// that partial template around:
//**********************************************************
$template_with_message = html_printf(
    $notification_template,
    [":html_message" => $_GET['message']]
);
echo html_printf(
    $template_with_message,
    [':attr_class' => 'alert']
);


////**********************************************************
//// Example of programmers accidentally trying to use user
//// input _WITHOUT_ going through the appropriate escaping:
////**********************************************************
//try {
//
//    $bad_tempate = '<span class=":attr_class">' . $_GET['message'] . '</span>';
//    echo html_printf(
//        $bad_tempate,
//        [':attr_class' => 'alert']
//    );
//
//    echo "This will never be reached, as the programmer mistake will be caught";
//    exit(-1);
//}
//catch (Esprintf\UnsafeTemplateException $ute) {
//    echo "Hooray! we caught this mistake!\n";
//    echo $ute->getMessage() . "\n";
//}


//**********************************************************
// Example of correctly using uri escaped
//**********************************************************



//**********************************************************
// Example of incorrectly using html escaped
//**********************************************************

$html = htmlEscape("This is some &perfectly normal text ");
$template = '<span class=":attr_class">Hello there</span>';

try {
    // Trying to use html escaped where html_attr is expected
    echo html_printf(
        $template,
        [':attr_class' => $html]
    );
}
catch (Esprintf\BadTypeException $bte) {
    echo "Hooray! we caught this mistake!\n";
    echo $ute->getMessage() . "\n";
}