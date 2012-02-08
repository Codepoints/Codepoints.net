<?php

require_once "lib/codepoint.class.php";
require_once "lib/unicoderange.class.php";
require_once "lib/unicodeblock.class.php";
require_once "lib/unicodeplane.class.php";
require_once "lib/views.php";

$db = new PDO('sqlite:'.dirname(__FILE__).'/ucd.sqlite');
$name = isset($_GET['name'])? $_GET['name'] : NULL;

if ($name !== NULL) {
    try {
        $plane = new UnicodePlane($name, $db);
    } catch (Exception $e) {
        $plane = NULL;
    }
    if ($plane) {
        print_view('plane.html', compact('plane'));
        exit;
    }
}

header('HTTP/1.0 404 Not Found');
print_view('plane_404.html', array('name'=>$name));

