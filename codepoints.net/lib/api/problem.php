<?php

require_once __DIR__.'/../tools.php';

$host = get_origin().'api/v1';

switch ($data) {
case "precondition_failed":
    $name = _('Prerequisite Missing');
    $info = sprintf(_("A prerequisite was not found. That means, that the action to be called does not exist. See %s/ for API usage."), $host);
    break;
case "request_uri_too_long":
    $name = _('Request Too Long');
    $info = _("The request body (or the data in the URL) was too large. Try to reduce it.");
    break;
case "bad_request":
    $name = _('Bad Request');
    $info = sprintf(_("The client made a bad request, for example, by not specifying a required parameter. See %s/ for API usage."), $host);
    break;
case "not_found":
    $name = _('Not Found');
    $info = _("The requested resource was not found. This can, for example, happen for non-existing codepoints.");
    break;
default:
    $name = _('Error');
    $info = _("An unspecific error occurred.");
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
