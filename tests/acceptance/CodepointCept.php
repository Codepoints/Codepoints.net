<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('check codepoint pages');

$categories = [
    'control' =>   ['U+0000',   200],
    'ascii' =>     ['U+004F',   200], # LATIN CAPITAL LETTER O
    'arabic' =>    ['U+0636',   200], # ARABIC LETTER DAD
    'rtl' =>       ['U+202E',   200], # RIGHT-TO-LEFT OVERRIDE
    'cjk' =>       ['U+4E15',   200], # CJK UNIFIED IDEOGRAPH-4E15
    'modifier' =>  ['U+A706',   200], # MODIFIER LETTER CHINESE TONE YIN RU
    'surrogate' => ['U+D801',   200],
    'pua' =>       ['U+E123',   200], # private-use code point
    'reserved' =>  ['U+FFF4',   404],
    'emoji' =>     ['U+1F6E3',  200], # MOTORWAY
    'plane2' =>    ['U+20000',  200], # existing 3rd plane character
    'nonchar' =>   ['U+3FFFF',  200], # non-character
    'unas_astr' => ['U+69876A', 404], # Astral unassigned code point
    'pua_astr' =>  ['U+10456A', 200], # Astral private-use code point
    'last' =>      ['U+10FFFF', 200],
    'beyond' =>    ['U+1123AF', 404], # Something beyond the last code point
    'multi-cp uc' => ['U+1FC4', 200], # a code point that has two code points as upper-case variants
];

foreach ($categories as $category => list($codepoint, $response_code)) {
    $I->expectTo('see code point info for a '.$category);
    $I->amOnPage('/'.$codepoint);
    $I->seeResponseCodeIs($response_code);
    /* make sure we see the footer */
    $I->seeElement('.page-footer');
    $I->see($codepoint, 'h1');
}
