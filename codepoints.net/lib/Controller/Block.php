<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Router\NotFoundException;
use Codepoints\Unicode\Block as UnicodeBlock;


class Block extends Controller {

    public function __invoke($data, array $env) : string {
        $block = new UnicodeBlock($data, $env['db']);
        $this->context += [
            'title' => $block->name,
            'page_description' => sprintf(
                __('The Unicode block %s contains the codepoints from U+%04X to U+%04X.'),
                $block->name, $block->first, $block->last),
            'block' => $block,
            'prev' => $block->prev,
            'next' => $block->next,
        ];
        return parent::__invoke($matches, $env);
    }

}
