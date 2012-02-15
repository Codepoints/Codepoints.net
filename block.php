<?php

require_once "lib/pagination.class.php";
require_once "lib/codepoint.class.php";
require_once "lib/unicoderange.class.php";
require_once "lib/unicodeblock.class.php";
require_once "lib/unicodeplane.class.php";
require_once "lib/views.php";

$db = new PDO('sqlite:'.dirname(__FILE__).'/ucd.sqlite');
$name = isset($_GET['name'])? $_GET['name'] : NULL;

if ($name !== NULL) {
    try {
        $block = new UnicodeBlock($name, $db);
    } catch(Exception $e) {
        $block = NULL;
    }
    if ($block) {
        print_view('block.html', compact('block'));
        exit;
    }
}

header('HTTP/1.0 404 Not Found');
print_view('block_404.html', array('name'=>$name));

