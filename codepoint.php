<?php

require_once "lib/helpers.php";
require_once "lib/unicodedb.php";
require_once "lib/codepoint.class.php";
require_once "lib/unicoderange.class.php";
require_once "lib/unicodeblock.class.php";
require_once "lib/unicodeplane.class.php";
require_once "lib/views.php";

$db = new PDO('sqlite:'.dirname(__FILE__).'/ucd.sqlite');
$unidb = new UnicodeDB($db);

$cp = isset($_GET['cp'])? hexdec($_GET['cp']) : -1;

$codepoint = NULL;
if ($cp >= 0) {
    try {
        $codepoint = new Codepoint($cp, $db);
    } catch (Exception $e) {
    }
}

if ($codepoint === NULL) {
    header('HTTP/1.0 404 Not Found');
    print_view('codepoint_404.html', array());
    exit;
}

print_view('codepoint.html', array(
    'properties' => $unidb->getProperties(),
    'codepoint' => $codepoint));


