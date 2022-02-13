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
            SELECT id, `other` AS cp, name, gc
                FROM codepoint_confusables
            LEFT JOIN codepoints
                ON (codepoints.cp = codepoint_confusables.`other`)
            WHERE codepoint_confusables.cp = ?
            ORDER BY id, `order`
            ', $codepoint->id);
        if ($data) {
            foreach ($data as $v) {
                $id = $v['id'];
                unset($v['id']);
                if (! isset($confusables[$id])) {
                    $confusables[$id] = [];
                }
                $confusables[$id][] = Codepoint::getCached($v, $this->db);
            }
        }

        return $confusables;
    }

}
