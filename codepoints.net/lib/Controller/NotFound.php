<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Unicode\Codepoint;
use Codepoints\View;
use Exception;


class NotFound extends Controller {

    public function __invoke($match, Array $env) : string {
        if (! is_string($match)) {
            return '';
        }
        http_response_code(404);

        $title = __('Page not Found');
        $page_description = __('There is no content on this page.');
        $codepoint = null;
        $cps = [];

        if (preg_match('/^U\+([0-9A-Fa-f]+)$/', $match, $match2)) {
            $cp = hexdec($match2[1]);
            $title = sprintf(__('Codepoint U+%04X not Found'), $cp);
            $page_description = sprintf(__('The point U+%04X is no valid Unicode codepoint.'), $cp);
            $codepoint = Codepoint::getCached([
                'cp' => $cp,
                'name' => $cp <= 0x10FFFF? '<reserved>' : 'CECI N’EST PAS UNICODE',
                'gc' => $cp <= 0x10FFFF? 'Cn' : 'Xx'],
                $env['db']);
            $block = null;
            $plane = null;
            try {
                $block = $codepoint->block;
            } catch (Exception $e) {}
            try {
                $plane = $codepoint->plane;
            } catch (Exception $e) {}
            $this->context += [
                'codepoint' => $codepoint,
                'prev' => $codepoint->prev,
                'next' => $codepoint->next,
                'block' => $block,
                'plane' => $plane,
                'wikipedia' => null,
                'extra' => null,
                'othersites' => null,
                'relatives' => $codepoint->relatives,
                'confusables' => $codepoint->confusables,
            ];
        } elseif (strlen($match) < 128) {
            $cps = string_to_codepoints(rawurldecode($match), $env['db']);
        }

        $this->context += [
            'title' => $title,
            'header' => sprintf('%s %s', (string)$codepoint, $title),
            'page_description' => $page_description,
            'cps' => $cps,
            'match' => $match,
        ];
        $view = $codepoint? 'codepoint' : '404';
        return (new View($view))($this->context, $env);
    }

}
