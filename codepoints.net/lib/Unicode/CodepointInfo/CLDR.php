<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;


/**
 * fetch CLDR properties for the current code point
 */
class CLDR extends CodepointInfo {

    /**
     * return the CLDR data for this code point
     *
     * @return Array{ tts: ?string, tags: Array<string> }
     */
    public function __invoke(Codepoint $codepoint) : Array {
        /* tts = "text to speech" */
        $cldr_data = [ 'tts' => null, 'tags' => [], ];

        /* shortcut for PUA codepoints and non-characters: fetch precomposed
         * set. */
        if (is_pua($codepoint->id) || in_array($codepoint->gc, ['Cn', 'Xx'])) {
            return $cldr_data;
        }

        /* select all annotations */
        $data = $this->db->getAll('
            SELECT annotation, `type`
                FROM codepoint_annotation
            WHERE cp = ?
                AND lang = ?', $codepoint->id, $this->lang);
        if ($data) {
            $tts = array_filter($data, function (Array $item) : bool {
                return $item['type'] === 'tts';
            });
            if ($tts) {
                $cldr_data['tts'] = (string)(reset($tts)['annotation']);
            }
            $cldr_data['tags'] = array_map(
                function(Array $item) : string {
                    return $item['annotation'];
                }, array_filter($data, function (Array $item) use ($cldr_data) : bool {
                    return $item['type'] === 'tag' && $item['annotation'] !== $cldr_data['tts'];
                }));
        }
        return $cldr_data;
    }

}
