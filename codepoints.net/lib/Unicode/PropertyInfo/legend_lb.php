<?php

return [ # tr14
    'AI' => [__('Ambiguous (Alphabetic or Ideographic)'), __('Act like AL when the resolved EAW is N; otherwise, act as ID')],
    'AL' => [__('Alphabetic'), __('Are alphabetic characters or symbols that are used with alphabetic characters')],
    'B2' => [__('Break Opportunity Before and After'), __('Provide a line break opportunity before and after the character')],
    'BA' => [__('Break After'), __('Generally provide a line break opportunity after the character')],
    'BB' => [__('Break Before'), __('Generally provide a line break opportunity before the character')],
    'BK' => [__('Mandatory Break'), __('Cause a line break (after)')],
    'CB' => [__('Contingent Break Opportunity'), __('Provide a line break opportunity contingent on additional information')],
    'CJ' => [__('Conditional Japanese Starter'), __('Treat as NS or ID for strict or normal breaking.')],
    'CL' => [__('Close Punctuation'), __('Prohibit line breaks before')],
    'CM' => [__('Combining Mark'), __('Prohibit a line break between the character and the preceding character')],
    'CP' => [__('Close Parenthesis'), __('Prohibit line breaks before')],
    'CR' => [__('Carriage Return'), __('Cause a line break (after), except between CR and LF')],
    'EB' => [__('Emoji Base'), __('Do not break from following Emoji Modifier')],
    'EM' => [__('Emoji Modifier'), __('Do not break from preceding Emoji Base')],
    'EX' => [__('Exclamation/Interrogation'), __('Prohibit line breaks before')],
    'GL' => [__('Non-breaking (“Glue”)'), __('Prohibit line breaks before and after')],
    'H2' => [__('Hangul LV Syllable'), __('Form Korean syllable blocks')],
    'H3' => [__('Hangul LVT Syllable'), __('Form Korean syllable blocks')],
    'HL' => [__('Hebrew Letter'), __('Do not break around a following hyphen; otherwise act as Alphabetic')],
    'HY' => [__('Hyphen'), __('Provide a line break opportunity after the character, except in numeric context')],
    'ID' => [__('Ideographic'), __('Break before or after, except in some numeric context')],
    'IN' => [__('Inseparable'), __('Allow only indirect line breaks between pairs')],
    'IS' => [__('Infix Numeric Separator'), __('Prevent breaks after any and before numeric')],
    'JL' => [__('Hangul L Jamo'), __('Form Korean syllable blocks')],
    'JT' => [__('Hangul T Jamo'), __('Form Korean syllable blocks')],
    'JV' => [__('Hangul V Jamo'), __('Form Korean syllable blocks')],
    'LF' => [__('Line Feed'), __('Cause a line break (after)')],
    'NL' => [__('Next Line'), __('Cause a line break (after)')],
    'NS' => [__('Nonstarter'), __('Allow only indirect line breaks before')],
    'NU' => [__('Numeric'), __('Form numeric expressions for line breaking purposes')],
    'OP' => [__('Open Punctuation'), __('Prohibit line breaks after')],
    'PO' => [__('Postfix Numeric'), __('Do not break following a numeric expression')],
    'PR' => [__('Prefix Numeric'), __('Do not break in front of a numeric expression')],
    'QU' => [__('Quotation'), __('Act like they are both opening and closing')],
    'RI' => [__('Regional Indicator'), __('Keep pairs together. For pairs, break before and after other classes')],
    'SA' => [__('Complex Context Dependent (South East Asian)'), __('Provide a line break opportunity contingent on additional, language-specific context analysis')],
    'SG' => [__('Surrogate'), __('Do not occur in well-formed text')],
    'SP' => [__('Space'), __('Enable indirect line breaks')],
    'SY' => [__('Symbols Allowing Break After'), __('Prevent a break before, and allow a break after')],
    'WJ' => [__('Word Joiner'), __('Prohibit line breaks before and after')],
    'XX' => [__('Unknown'), __('Have as yet unknown line breaking behavior or unassigned code positions')],
    'ZWJ' => [__('Zero Width Joiner'), __('Prohibit line breaks within joiner sequences')],
    'ZW' => [__('Zero Width Space'), __('Provide a break opportunity')],
];
