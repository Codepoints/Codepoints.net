<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;


/**
 * fetch Unicode properties for a given code point
 */
class Properties extends CodepointInfo {

    private Array $private_use_properties = [
        'na' =>      '',
        'JSN' =>     '',
        'gc' =>      'Co',
        'ccc' =>     '0',
        'dt' =>      'none',
        'nt' =>      'None',
        'nv' =>      'NaN',
        'bc' =>      'L',
        'bpt' =>     'n',
        'Bidi_M' =>  0,
        'bmg' =>     '',
        'jt' =>      'U',
        'jg' =>      'No_Joining_Group',
        'ea' =>      'A',
        'lb' =>      'XX',
        'sc' =>      'Zzzz',
        'scx' =>     ['Zzzz'],
        'Dash' =>    0,
        'WSpace' =>  0,
        'Hyphen' =>  0,
        'QMark' =>   0,
        'Radical' => 0,
        'Ideo' =>    0,
        'UIdeo' =>   0,
        'IDSB' =>    0,
        'IDST' =>    0,
        'hst' =>     'NA',
        'DI' =>      0,
        'ODI' =>     0,
        'Alpha' =>   0,
        'OAlpha' =>  0,
        'Upper' =>   0,
        'OUpper' =>  0,
        'Lower' =>   0,
        'OLower' =>  0,
        'Math' =>    0,
        'OMath' =>   0,
        'Hex' =>     0,
        'AHex' =>    0,
        'NChar' =>   0,
        'VS' =>      0,
        'Bidi_C' =>  0,
        'Join_C' =>  0,
        'Gr_Base' => 0,
        'Gr_Ext' =>  0,
        'OGr_Ext' => 0,
        'Gr_Link' => 0,
        'STerm' =>   0,
        'Ext' =>     0,
        'Term' =>    0,
        'Dia' =>     0,
        'Dep' =>     0,
        'IDS' =>     0,
        'OIDS' =>    0,
        'XIDS' =>    0,
        'IDC' =>     0,
        'OIDC' =>    0,
        'XIDC' =>    0,
        'SD' =>      0,
        'LOE' =>     0,
        'Pat_WS' =>  0,
        'Pat_Syn' => 0,
        'GCB' =>     'XX',
        'WB' =>      'XX',
        'SB' =>      'XX',
        'CE' =>      0,
        'Comp_Ex' => 0,
        'NFC_QC' =>  1,
        'NFD_QC' =>  1,
        'NFKC_QC' => 1,
        'NFKD_QC' => 1,
        'XO_NFC' =>  0,
        'XO_NFD' =>  0,
        'XO_NFKC' => 0,
        'XO_NFKD' => 0,
        'FC_NFKC' => '',
        'CI' =>      0,
        'Cased' =>   0,
        'CWCF' =>    0,
        'CWCM' =>    0,
        'CWKCF' =>   0,
        'CWL' =>     0,
        'CWT' =>     0,
        'CWU' =>     0,
        'InSC' =>    'Other',
        'InPC' =>    'NA',
        'PCM' =>     0,
        'vo' =>      'R',
        'RI' =>      0,
        'isc' =>     '',
        'na1' =>     '',
        'Emoji' =>   0,
        'EPres' =>   0,
        'EMod' =>    0,
        'EBase' =>   0,
        'EComp' =>   0,
        'ExtPict' => 0,
    ];

    private Array $Cn_property_patch = [
        'gc' =>      'Cn',
        'bc' =>      'BN',
        'ea' =>      'N',
        'NChar' =>   1,
    ];

    private Array $Cs_property_patch = [
        'gc' =>      'Cs',
        'lb' =>      'SG',
        'ea' =>      'N',
    ];

    private Array $private_use_self_references = [
        'dm', 'suc', 'slc', 'stc', 'uc', 'lc', 'tc', 'scf', 'cf', 'NFKC_CF', ];

    /**
     * return the official Unicode properties for a code point
     */
    public function __invoke(Codepoint $codepoint) : Array {
        $properties = [];

        /* shortcut for PUA codepoints and non-characters: fetch precomposed
         * set. */
        if (is_pua($codepoint->id) || is_surrogate($codepoint->id) ||
            in_array($codepoint->gc, ['Cn', 'Xx'])) {

            $patch = [];
            if ($codepoint->gc === 'Cn') {
               $patch = $this->Cn_property_patch;
            } elseif ($codepoint->gc === 'Cs') {
               $patch = $this->Cs_property_patch;
            }
            $properties = $patch + $this->private_use_properties;
            $properties['age'] = ($codepoint->id <= 0xF8FF? '1.1' : '2.0');
            foreach ($this->private_use_self_references as $prop) {
                $properties[$prop] = $codepoint;
            }
            return $this->sortProperties($properties);
        }

        $data = $this->db->getOne('
            SELECT props.*, script.sc AS sc
                FROM codepoint_props props
            LEFT JOIN codepoint_script script
                ON (script.cp = props.cp
                    AND script.`primary` = 1)
            WHERE props.cp = ?
            LIMIT 1', $codepoint->id);
        if ($data) {
            $properties += $data;
        }

        /* select all other scripts, where this code point might appear in
         * (the scx property). */
        $data = $this->db->getAll('
            SELECT sc AS scx
                FROM codepoint_script
            WHERE cp = ?
                AND `primary` = 0', $codepoint->id);
        $properties['scx'] = [];
        if ($data) {
            $properties['scx'] = array_map(function(Array $item) : string { return $item['scx']; }, $data);
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
                    $properties[$rel] = Codepoint::getCached($v, $this->db);
                } else {
                    if (array_key_exists($rel, $properties) && ! is_array($properties[$rel])) {
                        throw new \Exception('double property '.$rel);
                    }
                    if (! array_key_exists($rel, $properties)) {
                        $properties[$rel] = [];
                    }
                    $properties[$rel][$v['order'] - 1] = Codepoint::getCached($v, $this->db);
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
            $priority = ['age', 'na', 'na1', 'blk', 'gc', 'sc', 'bc', 'ccc',
                'dt', 'dm', 'Lower', 'slc', 'lc', 'Upper', 'suc', 'uc', 'stc',
                'tc', 'cf'];
            if (in_array($a, $priority) && in_array($b, $priority)) {
                return array_search($a, $priority) <=> array_search($b, $priority);
            } elseif (in_array($a, $priority)) {
                return -1;
            } elseif (in_array($b, $priority)) {
                return 1;
            } elseif ($a[0] === 'k' && $b[0] !== 'k') {
                return 1;
            } elseif ($a[0] !== 'k' && $b[0] === 'k') {
                return -1;
            }
            return strcasecmp($a, $b);
        });
        return $properties;
    }

}
