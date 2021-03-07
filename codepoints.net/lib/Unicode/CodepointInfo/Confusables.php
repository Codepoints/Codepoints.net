<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;


/**
 * fetch code points possibly confusable with this one
 */
class Confusables extends CodepointInfo {

    /**
     * return a list of confusable characters
     */
    public function __invoke(Codepoint $codepoint) : Array {
        $confusables = [];

        $data = $this->db->getAll('
            SELECT other AS cp, `order`, name, gc
                FROM codepoint_confusables
            LEFT JOIN codepoints
                ON (codepoints.cp = codepoint_confusables.other)
            WHERE codepoint_confusables.cp = ?
            UNION
            SELECT codepoints.cp, `order`, name, gc
                FROM codepoint_confusables
            LEFT JOIN codepoints
                ON (codepoints.cp = codepoint_confusables.cp)
            WHERE other = ?', $codepoint->id, $codepoint->id);
        if ($data) {
            foreach ($data as $v) {
                /* TODO this is partially useless. Some confusables are
                 * compounds and we simply dump all of them in a list */
                $confusables[] = Codepoint::getCached($v, $this->db);
            }
        }

        return $confusables;
    }

}
