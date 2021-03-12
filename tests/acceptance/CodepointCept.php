<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('check codepoint pages');

$categories = [
    'ascii' =>     ['U+004F',   200], # LATIN CAPITAL LETTER O
    'control' =>   ['U+0000',   200],
    'nonchar' =>   ['U+3FFFF',  200],
    'reserved' =>  ['U+FFF4',   404],
    'emoji' =>     ['U+1F6E3',  200], # MOTORWAY
    'plane2' =>    ['U+20000',  200],
    'last' =>      ['U+10FFFF', 200],
    'beyond' =>    ['U+1123AF', 404], # Something beyond the last code point
    'arabic' =>    ['U+0636',   200], # ARABIC LETTER DAD
    'cjk' =>       ['U+4E15',   200], # CJK UNIFIED IDEOGRAPH-4E15
    'modifier' =>  ['U+A706',   200], # MODIFIER LETTER CHINESE TONE YIN RU
    'surrogate' => ['U+D801',   200],
    'pua' =>       ['U+E123',   200],
    'rtl' =>       ['U+202E',   200], # RIGHT-TO-LEFT OVERRIDE
];

foreach ($categories as $category => list($codepoint, $response_code)) {
    $I->expectTo('see code point info for a '.$category);
    $I->amOnPage('/'.$codepoint);
    $I->seeResponseCodeIs($response_code);
    $I->see('Blog');
    $I->see($codepoint, 'h1');
}
