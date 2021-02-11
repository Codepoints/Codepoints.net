<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Unicode\Codepoint as UnicodeCodepoint;


class Codepoint extends Controller {

    public function __invoke($matches, array $env) : string {
        $cp = hexdec($matches[1]);
        $data = $env['db']->getOne('SELECT cp, name FROM codepoints WHERE cp = ?', $cp);
        $codepoint = new UnicodeCodepoint($data, $env['db']);

        $this->context += [
            'title' => sprintf('%s %s', (string)$codepoint, $codepoint->name),
            //'page_description' => sprintf(
            //    __('%s, codepoint U+%04X %s in Unicode, is located in the block “%s”. It belongs to the %s script and is a %s.'),
            //    $codepoint->getSafeChar(),
            //    $codepoint->getId(), $codepoint->getName(), $block? $block->getName() : '-', $info->getLabel('sc', $props['sc']), $info->getLabel('gc', $props['gc']));
            'codepoint' => $codepoint,
            'prev' => $codepoint->prev,
            'next' => $codepoint->next,
            'block' => $codepoint->block,
            'plane' => $codepoint->block->plane,
        ];
        return parent::__invoke($matches, $env);
    }

}
