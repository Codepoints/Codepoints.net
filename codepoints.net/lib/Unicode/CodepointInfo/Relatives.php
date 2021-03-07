<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;


/**
 * fetch code points related with this one
 */
class Relatives extends CodepointInfo {

    /**
     * return a list of related characters
     */
    public function __invoke(Codepoint $codepoint) : Array {
        $relatives = [];

        $data = $this->db->getAll('
            SELECT cp, name, gc
                FROM codepoint_relation
            LEFT JOIN codepoints
                ON (codepoints.cp = codepoint_relation.cp)
            WHERE other = ? AND cp != ? GROUP BY cp',
            $codepoint->id, $codepoint->id);
        if ($data) {
            foreach ($data as $v) {
                $relatives[] = Codepoint::getCached($v, $this->db);
            }
        }

        return $relatives;
    }

}
