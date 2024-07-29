<?php

namespace Codepoints\Unicode;


/**
 * base class for info providers for code points
 */
class EncodingInfo {

    /**
     * @return string
     */
    public static function getLabel(string $id) : string {
        return match ($id) {
            'ascii' => 'ASCII',
            'big5' => 'Big5',
            'big5hkscs' => 'Big5-HKSCS',
            'euc_jis_2004' => 'EUC-JIS-2004',
            'euc_jisx0213' => 'EUC-JISx0213',
            'euc_jp' => 'EUC-JP',
            'euc_kr' => 'EUC-KR',
            'gb18030' => 'GB 18030',
            'gb2312' => 'GB 2312',
            'gbk' => 'GBK / Codepage 936',
            'hz' => 'HZ',
            'iso2022_jp'  => 'ISO/IEC 2022-JP',
            'iso2022_jp_1'  => 'ISO/IEC 2022-JP-1',
            'iso2022_jp_2'  => 'ISO/IEC 2022-JP-2',
            'iso2022_jp_2004'  => 'ISO/IEC 2022-JP-2004',
            'iso2022_jp_3'  => 'ISO/IEC 2022-JP-3',
            'iso2022_jp_ext'  => 'ISO/IEC 2022-JP-Ext',
            'iso2022_kr'  => 'ISO/IEC 2022-KR',
            'iso8859_2'  => 'ISO/IEC 8859-2',
            'iso8859_3'  => 'ISO/IEC 8859-3',
            'iso8859_4'  => 'ISO/IEC 8859-4',
            'iso8859_5'  => 'ISO/IEC 8859-5',
            'iso8859_6'  => 'ISO/IEC 8859-6',
            'iso8859_7'  => 'ISO/IEC 8859-7',
            'iso8859_8'  => 'ISO/IEC 8859-8',
            'iso8859_9'  => 'ISO/IEC 8859-9',
            'iso8859_10' => 'ISO/IEC 8859-10',
            'iso8859_11' => 'ISO/IEC 8859-11',
            'iso8859_13' => 'ISO/IEC 8859-13',
            'iso8859_14' => 'ISO/IEC 8859-14',
            'iso8859_15' => 'ISO/IEC 8859-15',
            'iso8859_16' => 'ISO/IEC 8859-16',
            'johab' => 'Johab',
            'koi8_r' => 'KOI8-R',
            'koi8_t' => 'KOI8-T',
            'koi8_u' => 'KOI8-U',
            'kz1048' => 'KZ-1048',
            'latin-1' => 'ISO/IEC 8859-1 / Latin-1',
            'mac_cyrillic' => 'Mac OS Cyrillic',
            'mac_greek' => 'Mac OS Greek',
            'mac_iceland' => 'Mac OS Icelandic',
            'mac_latin2' => 'Mac OS Central European',
            'mac_roman' => 'Mac OS Roman',
            'mac_turkish' => 'Mac OS Turkish',
            'ptcp154' => 'PTCP 154',
            'shift_jis' => 'Shift JIS',
            'shift_jis_2004' => 'Shift JIS 2004',
            'shift_jisx0213' => 'Shift JIS X 0213',
            default => preg_replace_callback('/^cp([0-9]+)(ms)?$/', function($m) {
                return 'Codepage '.$m[1].(count($m) > 2? ' (Microsoft)' : '');
            }, $id),
        };
    }

}
