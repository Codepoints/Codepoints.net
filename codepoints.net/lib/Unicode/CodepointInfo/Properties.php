<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;


/**
 * fetch Unicode properties for a given code point
 */
class Properties extends CodepointInfo {

    /**
     * return the official Unicode properties for a code point
     */
    public function __invoke(Codepoint $codepoint, Array $args) : array {
        $properties = [];

        $data = $this->db->getOne('
            SELECT props.*, script.sc AS script
                FROM codepoint_props props
            LEFT JOIN codepoint_script script
                ON (script.cp = props.cp
                    AND script.`primary` = 1)
            WHERE props.cp = ?
            LIMIT 1', $codepoint->id);
        if ($data) {
            $properties = $data;
        }

        /* select all other scripts, where this code point might appear in
         * (the scx property). */
        $data = $this->db->getAll('SELECT GROUP_CONCAT(sc SEPARATOR \' \') AS scx
                         FROM codepoint_script
                        WHERE cp = ?
                          AND `primary` = 0
                     GROUP BY cp', $codepoint->id);
        $properties['scx'] = null;
        if ($data) {
            $properties['scx'] = $data;
        }

        /* fetch all related code points (e.g., uppercase) */
        $data = $this->db->getAll('
            SELECT relation, other AS cp, `order`, name, gc
                FROM codepoint_relation
            LEFT JOIN codepoints
                ON (codepoints.cp = codepoint_relation.other)
            WHERE codepoint_relation.cp = ?
            ORDER BY `order`', $codepoint->id);
        if ($data) {
            foreach ($data as $v) {
                if ($v['order'] == 0) {
                    $properties[$v['relation']] = new Codepoint($v, $this->db);
                } else {
                    if (! array_key_exists($v['relation'], $properties)) {
                        $properties[$v['relation']] = [];
                    }
                    $properties[$v['relation']][$v['order'] - 1] = new Codepoint($v, $this->db);
                }
            }
        }

        return $properties;
    }

}
