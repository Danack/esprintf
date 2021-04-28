<?php

require_once __DIR__ . "/vendor/autoload.php";

if (function_exists('is_literal') !== true) {
    echo "This example requires the wip 'is_literal', and can't be run without it.";
    exit(-1);
}

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
echo esprintf(
    $notification_template,
    [':attr_class' => 'alert', ":html_message" => $_GET['message']]
);

//**********************************************************
// Example of partially filling in the template, and passing
// that partial template around:
//**********************************************************
$template_with_message = esprintf(
    $notification_template,
    [":html_message" => $_GET['message']]
);
echo esprintf(
    $template_with_message,
    [':attr_class' => 'alert']
);


//**********************************************************
// Example of programmers accidentally trying to use user
// input _WITHOUT_ going through the appropriate escaping:
//**********************************************************
try {

    $bad_tempate = '<span class=":attr_class">' . $_GET['message'] . '</span>';
    echo esprintf(
        $bad_tempate,
        [':attr_class' => 'alert']
    );

    echo "This will never be reached, as the programmer mistake will be caught";
    exit(-1);
}
catch (Esprintf\UnsafeTemplateException $ute) {
    echo "Hooray! we caught this mistake!\n";
    echo $ute->getMessage() . "\n";
}
