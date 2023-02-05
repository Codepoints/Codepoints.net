<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Api\Exception as ApiException;
use MatomoTracker;


class Api extends Controller {

    /**
     * @param Array{action: string, data?: string, 0?: string, 1?: string, 2?: string} $match
     */
    public function __invoke($match, Array $env) : string {
        try {
            $content = $this->run(
                $match['action'],
                isset($match['data']) ? $match['data'] : '', $env);
            if (! $content) {
                header('Content-Type: text/plain; charset=UTF-8');
                throw new ApiException('not found',
                    ApiException::NOT_FOUND);
            }
        } catch (ApiException $error) {
            http_response_code($error->getCode());
            header('Content-Type: text/plain; charset=UTF-8');
            $content = $error->getMessage();
        }
        $this->track($match['action']);
        return $content;
    }

    private function run(string $action, string $data, Array $env) : string {
        if (strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD')) === 'DELETE') {
            throw new ApiException(
                __('Ye?h, th?nks! You er?sed this codepoint. Are you h?ppy now?'),
                ApiException::BAD_REQUEST);
        }
        if (in_array(strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD')), ['PUT', 'POST'])) {
            throw new ApiException(
                __('To create a new codepoint, please mail it to unicode@unicode.org.'),
                ApiException::BAD_REQUEST);
        }
        $handler_class = '\\Codepoints\\Api\\Runner\\'.ucwords($action);
        if (! class_exists($handler_class)) {
            throw new ApiException(
                'method not found: '.$action,
                ApiException::NOT_FOUND);
        }
        $handler = new $handler_class($env);

        return $handler->handle($data);
    }

    private function track(string $action) : void {
        /* support Do-Not-Track header */
        if (filter_input(INPUT_SERVER, 'HTTP_DNT') === '1') {
            return;
        }
        $matomoTracker = new MatomoTracker(4, 'https://stats.codepoints.net/');
        /* make sure this blocks the API as little as possible */
        $matomoTracker->setRequestTimeout(1);
        $matomoTracker->setUrlReferrer($_SERVER['HTTP_REFERER'] ?? '');
        $matomoTracker->setUserAgent($_SERVER['HTTP_USER_AGENT'] ?? '');
        $matomoTracker->disableCookieSupport();
        $matomoTracker->doTrackPageView('API v1: '.$action);
    }

}
