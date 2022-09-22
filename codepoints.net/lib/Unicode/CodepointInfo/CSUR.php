<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;


/**
 * fetch CSUR data for the current PUA code point
 */
class CSUR extends CodepointInfo {

    /**
     * return the CSUR data for this code point
     *
     * @return Array{ name: ?string }
     */
    public function __invoke(Codepoint $codepoint) : Array {
        $csur_data = [ 'name' => null, ];

        /* shortcut for non-PUA codepoints */
        if (! is_pua($codepoint->id)) {
            return $csur_data;
        }

        $data = $this->db->getOne('
            SELECT name
                FROM csur
            WHERE cp = ?', $codepoint->id);
        if ($data && isset($data['name'])) {
            $csur_data['name'] = (string)$data['name'];
        }
        return $csur_data;
    }

}
