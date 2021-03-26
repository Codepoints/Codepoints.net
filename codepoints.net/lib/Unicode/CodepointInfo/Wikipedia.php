<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;


/**
 * provide wikipedia information for a given code point
 */
class Wikipedia extends CodepointInfo {

    /**
     * return the Wikipedia abstract for a code point
     *
     * If there is no abstract in the current language, try the english one.
     */
    public function __invoke(Codepoint $codepoint) : ?Array {
        $uc = $codepoint->id;
        if ($codepoint->gc === 'Ll' &&
            $codepoint->properties['uc'] instanceof Codepoint) {
            $uc = $codepoint->properties['uc']->id;
        }
        $data = $this->db->getOne('SELECT abstract, lang, src
                FROM codepoint_abstract
            WHERE (cp = ? OR cp = ?)
                AND (lang = ? OR lang = "en")
            ORDER BY lang = ? DESC, cp = ? DESC
            LIMIT 1', $codepoint->id, $uc, $this->lang, $this->lang, $codepoint->id);
        if (! $data) {
            return null;
        }
        return $data;
    }

}
