<?php

require_once __DIR__.'/../tools.php';

$host = get_origin().'api/v1';

return array(
    "codepoint_url" => "$host/codepoint/{codepoint}{?property*}",
    "block_url" => "$host/block/{block}",
    "plane_url" => "$host/plane/{plane}",
    "glyph_url" => "$host/glyph/{codepoint}",
    "name_url" => "$host/name/{codepoint}",
    "transform_url" => "$host/transform/{action}/{data}",
    "filter_url" => "$host/filter/{data}{?property*}",
    "property_url" => "$host/property/{property}",
    "script_url" => "$host/script/{iso}",
    "patterns" => array(
        "codepoint" => "/^(10[A-F0-9]{4}|0[A-F0-9]{5})$/i",
        "data" => "/^.{1,1024}$/",
        "iso" => "/^[A-Z][a-z]{3}$/",
    ),
);

#EOF
