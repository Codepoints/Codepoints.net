<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Unicode\Codepoint;
use Codepoints\View;
use Exception;


class NotFound extends Controller {

    public function __invoke($match, Array $env) : string {
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
            $intlist = array_map(
                function(string $c) : int { return mb_ord($c); },
                preg_split('//u', rawurldecode($match), -1, PREG_SPLIT_NO_EMPTY));
            $list = join(',', array_unique($intlist));
            $data = $env['db']->getAll('SELECT cp, name, gc FROM codepoints
                WHERE cp IN ( '.$list.' ) ORDER BY FIELD( cp, '.$list.' )');
            if ($data) {
                $tmpcps = [];
                foreach ($data as $set) {
                    $tmpcps[$set['cp']] = Codepoint::getCached($set, $env['db']);
                }
                foreach ($intlist as $int) {
                    if (array_key_exists($int, $tmpcps)) {
                        $cps[] = $tmpcps[$int];
                    }
                }
            }
        }

        $this->context += [
            'title' => $title,
            'header' => sprintf('%s %s', (string)$codepoint, $title),
            'page_description' => $page_description,
            'cps' => $cps,
        ];
        $view = $codepoint? 'codepoint' : '404';
        return (new View($view))($this->context, $env);
    }

}
