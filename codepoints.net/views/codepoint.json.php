<?php

if ($siblings[0]) {
    header('Link: ?cp=' . $siblings[0] . ';rel=prev', false);
}
if ($siblings[1]) {
    header('Link: ?cp=' . $siblings[1] . ';rel=next', false);
}

header('Content-Type: application/json;charset=UTF-8');
echo json_encode($cp);

