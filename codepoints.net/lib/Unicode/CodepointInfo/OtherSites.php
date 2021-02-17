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
        $other_sites['Fileformat.info'] = 'http://fileformat.info/info/unicode/char/'.$hex.'/index.htm';
        $other_sites[__('Unicode website')] = 'http://unicode.org/cldr/utility/character.jsp?a='.$hex.'';
        $other_sites[__('Reference rendering on Unicode.org')] = 'http://www.unicode.org/cgi-bin/refglyph?24-'.$hex.'';
        $abstract = $codepoint->getInfo('abstract');
        if ($abstract) {
            $other_sites[__('Wikipedia')] = $abstract['src'];
        }
        if ($codepoint->getInfo('properties')['kDefinition']) {
            $other_sites[__('Unihan Database')] = 'http://www.unicode.org/cgi-bin/GetUnihanData.pl?codepoint='.rawurlencode($codepoint->chr());
            $other_sites[__('Chinese Text Project')] = 'http://ctext.org/dictionary.pl?if=en&amp;char='.rawurlencode($codepoint->chr());
        }
        $other_sites['Graphemica'] = 'http://graphemica.com/'.rawurlencode($codepoint->chr());
        $other_sites['The UniSearcher'] = 'http://www.isthisthingon.org/unicode/index.phtml?glyph='.$hex;
        $other_sites['ScriptSource'] = 'http://scriptsource.org/char/U'.printf('%06X', $codepoint->id);
        return $other_sites;
    }

}
