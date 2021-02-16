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
                $rel = $v['relation'];
                if ($v['order'] == 0) {
                    $properties[$rel] = new Codepoint($v, $this->db);
                } else {
                    if (array_key_exists($rel, $properties) && ! is_array($properties[$rel])) {
                        throw new \Exception('double property '.$rel);
                    }
                    if (! array_key_exists($rel, $properties)) {
                        $properties[$rel] = [];
                    }
                    $properties[$rel][$v['order'] - 1] = new Codepoint($v, $this->db);
                }
            }
        }

        return $this->sortProperties($properties);
    }

    /**
     * sort by "important first", then literal (except k*), then the k*
     * properties.
     */
    private function sortProperties(Array $properties) : Array {
        uksort($properties, function(string $a, string $b) : int {
            $n = strcasecmp($a, $b);
            if ($n === 0) {
                return 0;
            }
            $r = ['age', 'na', 'na1', 'blk', 'gc', 'sc', 'bc', 'ccc',
                'dt', 'dm', 'Lower', 'slc', 'lc', 'Upper', 'suc', 'uc',
                'stc', 'tc', 'cf'];
            $r2 = [];
            for ($i = 0, $c = count($r); $i < $c; $i++) {
                if ($a === $r[$i]) {
                    if (in_array($b, $r2)) {
                        return 1;
                    } else {
                        return -1;
                    }
                } elseif ($b === $r[$i]) {
                    if (in_array($a, $r2)) {
                        return -1;
                    } else {
                        return 1;
                    }
                } elseif ($a[0] === 'k' && $b[0] === 'k') {
                    if ($a[1] === 'I' && $b[1] !== 'I') {
                        return -1;
                    } elseif ($a[1] !== 'I' && $b[1] === 'I') {
                        return 1;
                    } else {
                        return strcasecmp($a, $b);
                    }
                } else {
                    $r2[] = $r[$i];
                }
            }
            return strcasecmp($a, $b);
        });
        return $properties;
    }

}
