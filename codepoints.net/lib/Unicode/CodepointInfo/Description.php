<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;
use Codepoints\View;


/**
 * provide a descriptive text about this code point
 */
class Description extends CodepointInfo {

    /**
     *
     */
    public function __invoke(Codepoint $codepoint) : string {
        try {
            $block = $codepoint->block;
        } catch (\Exception $e) {
            $block = null;
        }
        return (new View('partials/codepoint-description'))([
            'codepoint' => $codepoint,
            'plane' => $codepoint->plane,
            'block' => $block,
            'props' => $codepoint->properties,
            'info' => $this->info,
            'pronunciation' => $codepoint->pronunciation,
            'aliases' => $codepoint->aliases,
            'relatives' => $codepoint->relatives,
            'confusables' => $codepoint->confusables,
            'vs15' => Codepoint::getCached([
                'cp' => 0xFE0E,
                'name' => 'VARIATION SELECTOR-15',
                'gc' => 'Mn',
            ], $this->db),
            'vs16' => Codepoint::getCached([
                'cp' => 0xFE0F,
                'name' => 'VARIATION SELECTOR-16',
                'gc' => 'Mn',
            ], $this->db),
            'db' => $this->db,
        ]);
    }

}
