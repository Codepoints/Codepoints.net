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
);

#EOF
