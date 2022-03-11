<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Router\NotFoundException;
use Codepoints\Unicode\Codepoint as UnicodeCodepoint;
use Codepoints\Unicode\CodepointInfo\Aliases;
use Codepoints\Unicode\CodepointInfo\CLDR;
use Codepoints\Unicode\CodepointInfo\Confusables;
use Codepoints\Unicode\CodepointInfo\Description;
use Codepoints\Unicode\CodepointInfo\Extra;
use Codepoints\Unicode\CodepointInfo\ImageSource;
use Codepoints\Unicode\CodepointInfo\OtherSites;
use Codepoints\Unicode\CodepointInfo\Pronunciation;
use Codepoints\Unicode\CodepointInfo\Properties;
use Codepoints\Unicode\CodepointInfo\Relatives;
use Codepoints\Unicode\CodepointInfo\Representation;
use Codepoints\Unicode\CodepointInfo\Wikipedia;
use Exception;


class Codepoint extends Controller {

    public function __invoke($match, Array $env) : string {
        new Aliases($env);
        new CLDR($env);
        new Confusables($env);
        new Description($env);
        new Extra($env);
        new ImageSource($env);
        new OtherSites($env);
        new Pronunciation($env);
        new Properties($env);
        new Relatives($env);
        new Representation($env);
        new Wikipedia($env);

        $codepoint = get_codepoint(hexdec($match[1]), $env['db']);
        if (! $codepoint) {
            throw new NotFoundException('no character found');
        }
        $block = null;
        $plane = null;
        $page_description = sprintf(
            __('%s, codepoint U+%04X %s in Unicode, is not located in any block. It is a %s.'),
            $codepoint->chr(),
            $codepoint->id,
            $codepoint->name,
            $env['info']->getLegend('gc', $codepoint->gc));
        try {
            $block = $codepoint->block;
            $page_description = sprintf(
                __('%s, codepoint U+%04X %s in Unicode, is located in the block “%s”. It belongs to the %s script and is a %s.'),
                $codepoint->chr(),
                $codepoint->id,
                $codepoint->name,
                $block->name,
                array_get($env['info']->script, $codepoint->properties['sc']),
                $env['info']->getLegend('gc', $codepoint->gc, $codepoint->gc));
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
            'relatives' => $codepoint->relatives,
            'confusables' => $codepoint->confusables,
            /* we need the DB, because for Unihan characters we want to render
             * related codepoint instances */
            'db' => $env['db'],
        ];
        return parent::__invoke($match, $env);
    }

}
