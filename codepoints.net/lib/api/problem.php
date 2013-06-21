<?php

require_once __DIR__.'/../tools.php';

$host = get_origin().'api/v1';

switch ($data) {
case "prerequisite_missing":
    $name = 'Prerequisite Missing';
    $info = "A prerequisite was not found. That means, that the action to be called does not exist. See $host/ for API usage.";
    break;
case "request_too_long":
    $name = 'Request Too Long';
    $info = "The request body (or the data in the URL) was too large. Try to reduce it.";
    break;
case "bad_request":
    $name = 'Bad Request';
    $info = "The client made a bad request, for example, by not specifying a required parameter. See $host/ for API usage.";
    break;
case "not_found":
    $name = 'Not Found';
    $info = "The requested resource was not found. This can, for example, happen for non-existing codepoints.";
    break;
default:
    $name = 'Error';
    $info = "An unspecific error occurred.";
    break;
}

return array(
    'name' => $name,
    'description' => $info,
    'api_root' => $host.'/',
    'api_version' => '1',
    'bugtracker' => 'https://github.com/Boldewyn/Codepoints.net/issues',
    'feedback' => 'info@codepoints.net',
    'twitter' => 'https://twitter.com/CodepointsNet',
);

#EOF
