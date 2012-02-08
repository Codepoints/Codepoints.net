<?php

require_once "lib/unicodedb.php";
require_once "lib/codepoint.class.php";
require_once "lib/unicoderange.class.php";
require_once "lib/unicodeblock.class.php";
require_once "lib/unicodeplane.class.php";
require_once "lib/views.php";

$db = new PDO('sqlite:'.dirname(__FILE__).'/ucd.sqlite');
$unidb = new UnicodeDB($db);

$name = isset($_GET['name'])? $_GET['name'] : NULL;

if ($name !== NULL) {
    try {
        $plane = new UnicodePlane($name, $db);
    } catch (Exception $e) {
        $plane = NULL;
    }
    if ($plane) {
        $planes = $unidb->getPlanes();
        $prev = $next = NULL;
        for ($i = 0; $i < count($planes); $i++) {
            $b = $planes[$i];
            if (str_replace(' ', '_', strtolower($b['name'])) === $name) {
                if (isset($planes[$i+1])) {
                    $next = $planes[$i+1];
                }
                break;
            }
            $prev = $b;
        }
        print_view('plane.html', compact('plane', 'prev', 'next'));
        exit;
    }
}

header('HTTP/1.0 404 Not Found');
print_view('plane_404.html', array('name'=>$name));

