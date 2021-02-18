<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Router\NotFoundException;
use Codepoints\Unicode\Codepoint as UnicodeCodepoint;
use Codepoints\Unicode\CodepointInfo\Image;
use Codepoints\Unicode\CodepointInfo\Wikipedia;
use Codepoints\Unicode\CodepointInfo\Properties;
use Codepoints\Unicode\CodepointInfo\Extra;
use Codepoints\Unicode\CodepointInfo\OtherSites;
use Exception;


class Codepoint extends Controller {

    public function __invoke($match, Array $env) : string {
        new Wikipedia($env['db'], $env['lang']);
        new Properties($env['db'], $env['lang']);
        new Extra($env['db'], $env['lang']);
        new OtherSites($env['db'], $env['lang']);

        $cp = hexdec($match[1]);
        if (is_pua($cp)) {
            $data = [
                'cp' => $cp,
                'name' => 'PRIVATE USE CHARACTER',
                'gc' => 'Co',
            ];
        } else {
            $data = $env['db']->getOne('SELECT cp, name, gc FROM codepoints WHERE cp = ?', $cp);
        }
        if (! $data) {
            throw new NotFoundException('no character found');
        }
        $codepoint = new UnicodeCodepoint($data, $env['db']);
        $block = null;
        $plane = null;
        $page_description = sprintf(
            __('%s, codepoint U+%04X %s in Unicode, is not located in any block. It is a %s.'),
            $codepoint->chr(),
            $codepoint->id,
            $codepoint->name,
            $env['info']->get('legend')['gc'][$codepoint->gc]);
        try {
            $block = $codepoint->block;
            $page_description = sprintf(
                __('%s, codepoint U+%04X %s in Unicode, is located in the block “%s”. It belongs to the %s script and is a %s.'),
                $codepoint->chr(),
                $codepoint->id,
                $codepoint->name,
                $block->name,
                array_get($env['info']->get('script'), $codepoint->properties['sc']),
                array_get(array_get($env['info']->get('legend'), 'gc', []), $codepoint->gc, $codepoint->gc));
        } catch (Exception $e) {}
        try {
            $plane = $codepoint->plane;
        } catch (Exception $e) {}

        $this->context += [
            'title' => sprintf('%s %s', (string)$codepoint, $codepoint->name),
            'page_description' => $page_description,
            'codepoint' => $codepoint,
            'prev' => $codepoint->prev,
            'next' => $codepoint->next,
            'block' => $block,
            'plane' => $plane,
            'wikipedia' => $codepoint->wikipedia,
            'extra' => $codepoint->extra,
            'othersites' => $codepoint->othersites,
        ];
        return parent::__invoke($match, $env);
    }

}
