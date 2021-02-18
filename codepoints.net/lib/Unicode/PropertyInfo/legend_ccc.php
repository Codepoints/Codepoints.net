<?php

$ccc = [
    '0' =>   [__('Not Reordered'),        __('Spacing and enclosing marks; also many vowel and consonant signs, even if nonspacing')],
    '1' =>   [__('Overlay'),              __('Marks which overlay a base letter or symbol')],
    '6' =>   [__('Han Reading'),          __('Diacritic reading marks for CJK unified ideographs')],
    '7' =>   [__('Nukta'),                __('Diacritic nukta marks in Brahmi-derived scripts')],
    '8' =>   [__('Kana Voicing'),         __('Hiragana/Katakana voicing marks')],
    '9' =>   [__('Virama'),               __('Viramas')],
    '200' => [__('Attached Below Left'),  __('Marks attached at the bottom left')],
    '202' => [__('Attached Below'),       __('Marks attached directly below')],
    '204' => [   '' ,                     __('Marks attached at the top right')],
    '208' => [   '' ,                     __('Marks attached to the left')],
    '210' => [   '' ,                     __('Marks attached to the right')],
    '212' => [   '' ,                     __('Marks attached at the top left')],
    '214' => [__('Attached Above'),       __('Marks attached directly above')],
    '216' => [__('Attached Above Right'), __('Marks attached at the top right')],
    '218' => [__('Below Left'),           __('Distinct marks at the bottom left')],
    '220' => [__('Below'),                __('Distinct marks directly below')],
    '222' => [__('Below Right'),          __('Distinct marks at the bottom right')],
    '224' => [__('Left'),                 __('Distinct marks to the left')],
    '226' => [__('Right'),                __('Distinct marks to the right')],
    '228' => [__('Above Left'),           __('Distinct marks at the top left')],
    '230' => [__('Above'),                __('Distinct marks directly above')],
    '232' => [__('Above Right'),          __('Distinct marks at the top right')],
    '233' => [__('Double Below'),         __('Distinct marks subtending two bases')],
    '234' => [__('Double Above'),         __('Distinct marks extending above two bases')],
    '240' => [__('Iota Subscript'),       __('Greek iota subscript only')],
];

for ($i = 10; $i <= 199; $i++) {
    $ccc[(string)$i] = ['Ccc'.$i, sprintf(__('Fixed Position Class %d'), $i)];
}

return $ccc;
