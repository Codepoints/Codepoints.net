<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;


/**
 * fetch alias names for a code point
 */
class Aliases extends CodepointInfo {

    /**
     * return the official Unicode properties for a code point
     */
    public function __invoke(Codepoint $codepoint) : Array {
        return $this->db->getAll('
            SELECT cp, alias, `type`
            FROM codepoint_alias
            WHERE cp = ?', $codepoint->id);
    }

}
