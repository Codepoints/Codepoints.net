<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;


/**
 * determine the font name used to render a given code point
 */
class ImageSource extends CodepointInfo {

    /**
     * return the font name, if any, for the shown glyph
     */
    public function __invoke(Codepoint $codepoint) : ?string {
        $source = null;
        $data = $this->db->getOne('
            SELECT font
                FROM codepoint_image
            WHERE codepoint_image.cp = ?
            LIMIT 1', get_printable_codepoint($codepoint->id, $codepoint->gc));
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if ($data) {
            $source = $data['font'];
        }
        return $source;
    }

}
