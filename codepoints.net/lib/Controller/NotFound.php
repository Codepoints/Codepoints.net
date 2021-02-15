<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Unicode\Codepoint;
use Codepoints\View;
use Exception;


class NotFound extends Controller {

    public function __invoke($match, array $env) : string {
        $title = __('Page not Found');
        $page_description = __('There is no content on this page.');
        $codepoint = null;
        $block = null;
        $plane = null;

        if (preg_match('/^U\+([0-9A-Fa-f]+)$/', $match, $match2)) {
            $cp = hexdec($match2[1]);
            $title = sprintf(__('Codepoint U+%04X not Found'), $cp);
            $page_description = sprintf(__('The point U+%04X is no valid Unicode codepoint.'), $cp);
            $codepoint = new Codepoint([
                'cp' => $cp,
                'name' => $cp <= 0x10FFFF? '<reserved>' : '',
                'gc' => $cp <= 0x10FFFF? 'Cn' : 'Xx'],
                $env['db']);
            try {
                $block = $codepoint->block;
                try {
                    $plane = $codepoint->block->plane;
                } catch (Exception $e) {}
            } catch (Exception $e) {}
        }

        $this->context += [
            'title' => $title,
            'page_description' => $page_description,
            'codepoint' => $codepoint,
            'prev' => $codepoint? $codepoint->prev : null,
            'next' => $codepoint? $codepoint->next : null,
            'block' => $block,
            'plane' => $plane,
        ];
        return (new View('404'))($this->context, $env);
    }

}
