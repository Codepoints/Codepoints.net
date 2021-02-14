<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Router\NotFoundException;
use Codepoints\Unicode\Codepoint as UnicodeCodepoint;


class Codepoint extends Controller {

    public function __invoke($match, array $env) : string {
        $cp = hexdec($match[1]);
        $data = $env['db']->getOne('SELECT cp, name, gc FROM codepoints WHERE cp = ?', $cp);
        if (! $data) {
            throw new NotFoundException('no character found');
        }
        $codepoint = new UnicodeCodepoint($data, $env['db']);

        $this->context += [
            'title' => sprintf('%s %s', (string)$codepoint, $codepoint->name),
            'page_description' => sprintf(
                __('%s, codepoint U+%04X %s in Unicode, is located in the block “%s”. It belongs to the %s script and is a %s.'),
                $codepoint->chr(),
                $codepoint->id,
                $codepoint->name,
                $codepoint->block->name,
                '', ''),//$info->getLabel('sc', $props['sc']), $info->getLabel('gc', $props['gc']));
            'codepoint' => $codepoint,
            'prev' => $codepoint->prev,
            'next' => $codepoint->next,
            'block' => $codepoint->block,
            'plane' => $codepoint->block->plane,
        ];
        return parent::__invoke($match, $env);
    }

}
