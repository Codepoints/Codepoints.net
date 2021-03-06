<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;


/**
 * get representations in other systems for the current code point
 */
class Representation extends CodepointInfo {

    private Array $formats = [
        'C' => '\\U%08X',
        'C_low' => '\\u%04X',
        'CSS' => '\\%06X',
        'Excel' => '=UNICHAR(%d)',
        'Go' => '\\U%08X',
        'Go_low' => '\\u%04X',
        'HTML' => '&#x%04X;',
        'Perl' => '"\\x{%04X}"',
        'PHP' => '"\\u{%04X}"',
        'Python' => '\\U%08X',
        'Python_low' => '\\u%04X',
        'RFC 5137' => '\\u\'%04X\'',
        'Ruby' => '\\u{%04X}',
        'Rust' => '\\u{%04X}',
        'XML' => '&#x%04X;',
    ];

    /**
     * get a function to fetch representations of the current code point
     */
    public function __invoke(Codepoint $codepoint) : Callable {
        $formats = $this->formats;
        return function (string $in) use ($codepoint, $formats) : string {
            switch ($in) {
                case 'UTF-8':
                case 'UTF-16':
                case 'UTF-32':
                    return join(' ',
                        str_split(
                            strtoupper(
                                bin2hex(mb_chr($codepoint->id, $in))), 2));
                case 'URL':
                    return '%' . join('%',
                        str_split(
                            strtoupper(
                                bin2hex(mb_chr($codepoint->id, 'utf-8'))), 2));
                case 'JS':
                case 'JSON':
                case 'Java':
                case 'YAML':
                    return '\\u' . join('\\u',
                        str_split(strtoupper(bin2hex(mb_chr($codepoint->id, 'UTF-16'))), 4));
                default:
                    if ($codepoint->id < 0x10000 && array_key_exists($in.'_low', $formats)) {
                        return sprintf($formats[$in.'_low'], $codepoint->id);
                    }
                    if (array_key_exists($in, $formats)) {
                        return sprintf($formats[$in], $codepoint->id);
                    }
                    throw new \UnexpectedValueException();
            }
        };
    }

}
