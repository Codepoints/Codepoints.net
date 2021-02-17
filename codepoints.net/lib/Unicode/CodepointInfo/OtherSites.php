<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;


/**
 * provide links to other sites for a given code point
 */
class OtherSites extends CodepointInfo {

    /**
     * return a list of links to other sites for a code point
     */
    public function __invoke(Codepoint $codepoint, Array $args) : Array {
        $other_sites = [];
        $hex = sprintf('%04X', $codepoint->id);
        $other_sites[__('Unicode website')] = 'https://unicode.org/cldr/utility/character.jsp?a='.$hex.'';
        if (! in_array($codepoint->gc, ['Cn', 'Xx'])) {
            $other_sites[__('Reference rendering on Unicode.org')] = 'https://www.unicode.org/cgi-bin/refglyph?24-'.$hex.'';
            $other_sites['Fileformat.info'] = 'https://fileformat.info/info/unicode/char/'.$hex.'/index.htm';
            $other_sites['Graphemica'] = 'https://graphemica.com/'.rawurlencode($codepoint->chr());
            $other_sites['The UniSearcher'] = 'https://www.isthisthingon.org/unicode/index.phtml?glyph='.$hex;
        }
        $wikipedia = $codepoint->getInfo('wikipedia');
        if ($wikipedia) {
            $other_sites[__('Wikipedia')] = $wikipedia['src'];
        }
        if ($codepoint->getInfo('properties')['kDefinition']) {
            $other_sites[__('Unihan Database')] = 'https://www.unicode.org/cgi-bin/GetUnihanData.pl?codepoint='.rawurlencode($codepoint->chr());
            $other_sites[__('Chinese Text Project')] = 'https://ctext.org/dictionary.pl?if=en&amp;char='.rawurlencode($codepoint->chr());
        }
        $other_sites['ScriptSource'] = 'https://scriptsource.org/char/U'.sprintf('%06X', $codepoint->id);
        return $other_sites;
    }

}
