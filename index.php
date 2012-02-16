<?php

require_once "lib/unicodeplane.class.php";
require_once "lib/views.php";

$db = new PDO('sqlite:'.dirname(__FILE__).'/ucd.sqlite');

print_view('front', array(
    'planes' => UnicodePlane::getAll($db)));


// __END__
