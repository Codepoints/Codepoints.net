<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;
use \Michelf\Markdown;


/**
 * provide extra information for a given code point
 */
class Extra extends CodepointInfo {

    /**
     * return extra information for a code point from the /data/* files
     */
    public function __invoke(Codepoint $codepoint) : string {
        $root = dirname(dirname(dirname(__DIR__))) . sprintf('/data/U+%04X.%%s.md', $codepoint->id);
        $ext_file = sprintf($root, $this->lang);
        if (! is_file($ext_file)) {
            if ($this->lang === 'en') {
                return '';
            }
            $ext_file = sprintf($root, 'en');
            if (! is_file($ext_file)) {
                return '';
            }
        }

        $parser = new Markdown;
        $parser->empty_element_suffix = '>';
        $parser->url_filter_func = function(string $url) : string {
            if (substr($url, 0, 3) === 'cp:') {
                $url = '/'.substr($url, 3);
            }
            return $url;
        };
        return $parser->transform(file_get_contents($ext_file));
    }

}
