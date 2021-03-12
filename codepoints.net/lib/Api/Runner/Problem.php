<?php

namespace Codepoints\Api\Runner;

use Codepoints\Api\JsonRunner;


class Problem extends JsonRunner {

    protected function handle_request(string $data) : Array {
        switch ($data) {
            case 'precondition_failed':
                $name = __('Prerequisite Missing');
                $info = sprintf(__('A prerequisite was not found. That means, that the action to be called does not exist. See %s/ for API usage.'), 'https://codepoints.net/api/v1');
                break;
            case 'request_uri_too_long':
                $name = __('Request Too Long');
                $info = __('The request body (or the data in the URL) was too large. Try to reduce it.');
                break;
            case 'bad_request':
                $name = __('Bad Request');
                $info = sprintf(__('The client made a bad request, for example, by not specifying a required parameter. See %s/ for API usage.'), 'https://codepoints.net/api/v1');
                break;
            case 'not_found':
                $name = __('Not Found');
                $info = __('The requested resource was not found. This can, for example, happen for non-existing codepoints.');
                break;
            default:
                $name = __('Error');
                $info = __('An unspecific error occurred.');
                break;
        }

        return [
            'name' => $name,
            'description' => $info,
            'api_root' => 'https://codepoints.net/api/v1/',
            'api_version' => '1',
            'bugtracker' => 'https://github.com/Boldewyn/Codepoints.net/issues',
            'feedback' => 'info@codepoints.net',
            'twitter' => 'https://twitter.com/CodepointsNet',
        ];
    }

}
