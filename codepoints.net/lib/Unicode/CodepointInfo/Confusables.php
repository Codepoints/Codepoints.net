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
            SELECT id, `other` AS cp, name, gc, `order`
                FROM codepoint_confusables
            LEFT JOIN codepoints
                ON (codepoints.cp = codepoint_confusables.`other`)
            WHERE codepoint_confusables.cp = ?

            UNION

            SELECT id, codepoints.cp AS cp, name, gc, `order`
                FROM codepoint_confusables
            LEFT JOIN codepoints
                ON (codepoints.cp = codepoint_confusables.`cp`)
            WHERE codepoint_confusables.`other` = ?

            ORDER BY id, `order`
            ', $codepoint->id, $codepoint->id);
        if ($data) {
            foreach ($data as $v) {
                $id = $v['id'];
                unset($v['id']);
                unset($v['order']);
                if (! isset($confusables[$id])) {
                    $confusables[$id] = [];
                }
                $confusables[$id][] = Codepoint::getCached($v, $this->db);
            }
        }

        return $confusables;
    }

}
