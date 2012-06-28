<?php

/**
 * Common knowledge to this site
 */
class Knowledge {

    public static $archaicScripts = array(
        "Armi", "Avst", "Bali", "Bamu", "Brah", "Bugi", "Cari", "Cher", "Cprt",
        "Dsrt", "Egyp", "Glag", "Goth", "Ital", "Java", "Khar", "Kthi", "Linb",
        "Lyci", "Lydi", "Merc", "Mero", "Mtei", "Ogam", "Orkh", "Osma", "Phag",
        "Phli", "Phnx", "Prti", "Rjng", "Runr", "Samr", "Sarb", "Shaw", "Shrd",
        "Sylo", "Syrc", "Takr", "Tfng", "Ugar", "Xpeo", "Xsux",
    );

    public static $recentScripts = array(
        "Arab", "Armn", "Batk", "Beng", "Bopo", "Brai", "Buhd", "Cakm", "Cans",
        "Cham", "Copt", "Cyrl", "Deva", "Ethi", "Geor", "Grek", "Gujr", "Guru",
        "Hang", "Hani", "Hano", "Hebr", "Hira", "Kali", "Kana", "Khmr", "Knda",
        "Lana", "Laoo", "Latn", "Lepc", "Limb", "Lisu", "Mand", "Mlym", "Mong",
        "Mymr", "Nkoo", "Olck", "Orya", "Plrd", "Saur", "Sinh", "Sora", "Sund",
        "Tagb", "Tale", "Talu", "Taml", "Tavt", "Telu", "Tglg", "Thaa", "Thai",
        "Tibt", "Vaii", "Yiii",
    );

    public static $regionToBlock = array(
        'Africa' => array('Ethiopic', 'Ethiopic Extended', 'Ethiopic Extended-A',
            'Ethiopic Supplement', 'NKo', 'Osmanya', 'Tifinagh', 'Meroitic Cursive',
            'Meroitic Hieroglyphs', 'Bamum', 'Bamum Supplement', 'Vai',),
        'America' => array('Cherokee', 'Deseret', 'Unified Canadian Aboriginal Syllabics',
            'Unified Canadian Aboriginal Syllabics Extended'),
        'Central_Asia' => array('Mongolian', 'Phags-pa', 'Tibetan', 'Chakma',
            'Lepcha',),
        'Philippines' => array('Buhid', 'Hanunoo', 'Tagalog', 'Tagbanwa',
            'Batak', 'Javanese', 'Rejang', 'Sundanese', 'Sundanese Supplement'),
        'Europe' => array('Armenian', 'Basic Latin', 'Combining Diacritical Marks',
            'Combining Diacritical Marks Supplement', 'Combining Half Marks',
            'Coptic', 'Cypriot Syllabary', 'Cyrillic', 'Cyrillic Extended-A',
            'Cyrillic Extended-B', 'Cyrillic Supplement', 'Georgian',
            'Georgian Supplement', 'Glagolitic', 'Gothic', 'Greek and Coptic',
            'Greek Extended', 'IPA Extensions', 'Latin Extended Additional',
            'Latin Extended-A', 'Latin Extended-B', 'Latin Extended-C',
            'Latin Extended-D', 'Latin-1 Supplement', 'Linear B Ideograms',
            'Linear B Syllabary', 'Modifier Tone Letters', 'Ogham', 'Old Italic',
            'Phonetic Extensions', 'Phonetic Extensions Supplement', 'Runic',
            'Shavian', 'Spacing Modifier Letters', 'Ancient Greek Musical Notation',
            'Ancient Greek Numbers', 'Ancient Symbols', 'Byzantine Musical Symbols',
            'Aegean Numbers', 'Lycian', 'Phaistos Disc', 'Superscripts and Subscripts',),
        'Middle_East' => array('Alphabetic Presentation Forms', 'Arabic',
            'Old South Arabian', 'Arabic Extended-A', 'Arabic Mathematical Alphabetic Symbols',
            'Arabic Presentation Forms-A', 'Arabic Presentation Forms-B',
            'Arabic Supplement', 'Cuneiform', 'Hebrew', 'Old Persian',
            'Phoenician', 'Syriac', 'Ugaritic', 'Samaritan', 'Egyptian Hieroglyphs',
            'Avestan', 'Carian', 'Cuneiform Numbers and Punctuation',
            'Imperial Aramaic', 'Inscriptional Pahlavi', 'Inscriptional Parthian',
            'Lydian', 'Mandaic', 'Old Turkic',),
        'East_Asia' => array('Bopomofo', 'Bopomofo Extended', 'CJK Symbols and Punctuation',
            'CJK Compatibility', 'CJK Compatibility Forms', 'CJK Compatibility Ideographs',
            'CJK Compatibility Ideographs Supplement', 'CJK Radicals Supplement',
            'CJK Strokes', 'CJK Unified Ideographs', 'CJK Unified Ideographs Extension A',
            'CJK Unified Ideographs Extension B', 'CJK Unified Ideographs Extension C',
            'CJK Unified Ideographs Extension D', 'Hangul Compatibility Jamo',
            'Hangul Jamo', 'Hangul Jamo Extended-A', 'Hangul Jamo Extended-B',
            'Hangul Syllables', 'Hiragana', 'Ideographic Description Characters', 'Kanbun',
            'Kangxi Radicals', 'Katakana', 'Katakana Phonetic Extensions',
            'Yi Radicals', 'Yi Syllables', 'Yijing Hexagram Symbols', 'Vertical Forms',
            'Enclosed CJK Letters and Months', 'Enclosed Ideographic Supplement',
            'Counting Rod Numerals', 'Kana Supplement', 'Miao', 'Halfwidth and Fullwidth Forms',
            'Small Form Variants', ),
        'South_Asia' => array('Bengali', 'Devanagari', 'Devanagari Extended',
            'Common Indic Number Forms', 'Gujarati', 'Gurmukhi', 'Kannada', 'Kharoshthi',
            'Limbu', 'Malayalam', 'Oriya', 'Sinhala', 'Syloti Nagri', 'Tamil',
            'Telugu', 'Thaana', 'Brahmi', 'Meetei Mayek', 'Meetei Mayek Extensions',
            'Kaithi', 'Ol Chiki', 'Saurashtra', 'Sharada', 'Sora Sompeng',
            'Vedic Extensions', 'Takri',),
        'Southeast_Asia' => array('Balinese', 'Buginese', 'Khmer', 'Khmer Symbols',
            'Lao', 'Myanmar', 'Myanmar Extended-A', 'New Tai Lue', 'Tai Le',
            'Tai Tham', 'Tai Viet', 'Tai Xuan Jing Symbols', 'Thai', 'Cham',
            'Lisu', 'Kayah Li', 'Rumi Numeral Symbols',),
        'n' => array('Optical Character Recognition', 'Transport And Map Symbols',
            'Miscellaneous Symbols', 'Miscellaneous Technical', 'Musical Symbols',
            'Miscellaneous Symbols And Pictographs', 'Miscellaneous Symbols and Arrows',
            'Miscellaneous Mathematical Symbols-A', 'Miscellaneous Mathematical Symbols-B',
            'Playing Cards', 'Dingbats', 'Domino Tiles', 'Emoticons', 'Geometric Shapes',
            'Mahjong Tiles', 'Mathematical Alphanumeric Symbols', 'Mathematical Operators',
            'Control Pictures', 'Alchemical Symbols', 'Arrows', 'Block Elements',
            'Box Drawing', 'Currency Symbols', 'Supplemental Arrows-A', 'Supplemental Arrows-B',
            'Supplemental Mathematical Operators', 'Supplemental Punctuation',
            'Tags', 'Letterlike Symbols', 'Variation Selectors', 'Variation Selectors Supplement',
            'Number Forms', 'General Punctuation', 'Combining Diacritical Marks for Symbols',
            'Braille Patterns', 'Specials', 'Enclosed Alphanumerics', 'Enclosed Alphanumeric Supplement'),
    );

}


//__END__
