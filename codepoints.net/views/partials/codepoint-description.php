<?php
use Codepoints\Unicode\Codepoint;
/**
 * @var Array $props
 * @var Codepoint $codepoint
 * @var Codepoint $vs15
 * @var Codepoint $vs16
 * @var \Codepoints\Unicode\Plane $plane
 * @var ?\Codepoints\Unicode\Block $block
 * @var \Codepoints\Unicode\PropertyInfo $info
 * @var list<Codepoint> $confusables
 * @var Array $aliases
 * @var ?string $pronunciation
 * @var \Codepoints\Database $db
 */

?>
<!-- codepoint -->
<p><?php
    echo _f('{cp} was added in Unicode version {version} in {year}.', [
        'cp' => $codepoint,
        'version' => '<a rel="nofollow" href="'.q(url('search?age='.$props['age'])).'">'.
        q($props['age']).'</a>',
        'year' => array_get($info->age_to_year, $props['age'], $props['age']),
      ]).
    ' '.
    _f('{block,
          select,
          NONE {It doesn’t belong to a defined block but is located in the {plane}.}
          other {It belongs to the block {block} in the {plane}.}
        }', [
        'block' => $block? bl($block, 'up') : 'NONE',
        'plane' => pl($plane, 'up'),
      ]).
    ' '.
    _f('{deprecated,
          select,
          YES {This codepoint is {b_start}deprecated{b_end}.}
          other {}}', [
        'deprecated' => $props['Dep']? 'YES' : 'NO',
        'b_start' => '<strong>',
        'b_end' => '</strong>',
    ]);
?></p>

<!-- character -->
<p><?php
    echo _f('{sc,
      select,
      Zzzz  {This character is a {b_start}{gc_legend}{b_end} and has {b_start}no script{b_end} assigned.}
      Zyyy  {This character is a {b_start}{gc_legend}{b_end} and is {b_start}commonly{b_end} used, that is, in no specific script.}
      Zinh  {This character is a {b_start}{gc_legend}{b_end} and {b_start}inherits{b_end} its {glossary_start}script property{glossary_end} from the preceding character.}
      other {This character is a {b_start}{gc_legend}{b_end} and is mainly used in the {b_start}{sc_legend}{b_end} script.}
    }', [
        'sc' => $props['sc'],
        'gc_legend' => q($info->getLegend('gc', $props['gc'])),
        'sc_legend' => q(array_get($info->script, $props['sc'], $props['sc'])),
        'glossary_start' => '<cp-glossary-term term="sc">',
        'glossary_end' => '</cp-glossary-term>',
        'b_start' => '<strong>',
        'b_end' => '</strong>',
    ]);

    $buf = [];
    foreach($props['scx'] as $sc) {
        if (array_key_exists('sc', $props) && $sc !== $props['sc']) {
            $buf[] = '<strong>'.
                    q(array_get($info->script, $sc, $sc)).'</strong>';
        }
    }

    echo ' '.
    _f('{len,
        plural,
        =0 {}
        =1 {It is also used in the script {scx}.}
        other {It is also used in the scripts {scx}.}
    }', [
        'len' => count($buf),
        'scx' => join(', ', $buf),
    ]);

    $defn = isset($codepoint->properties['kDefinition'])? (string)$codepoint->properties['kDefinition'] : null;
    if ($defn) {
        echo ' ';
        printf(__('The Unihan Database defines it as <em>%s</em>.'),
            preg_replace_callback('/U\+([0-9A-F]{4,6})/', function(Array $m) use ($db) : string {
                return cp(Codepoint::getCached(['cp' => hexdec($m[1]), 'name' => $m[0], 'gc' => 'Lo'], $db));
            }, $defn));
    }

    if ($pronunciation) {
        echo ' ';
        printf(__('Its Pīnyīn pronunciation is <em>%s</em>.'), q($pronunciation));
    }

    echo ' '.
    _f('{nt, select,
        None {}
        De {The codepoint has the {b_start}decimal{b_end} value {b_start}{nv}{b_end}.}
        Di {The codepoint represents the {b_start}digit{b_end} {b_start}{nv}{b_end}.}
        Nu {The codepoint has the {b_start}numeric{b_end} value {b_start}{nv}{b_end}.}
        other {}
    }', [
        'nt' => q($props['nt']),
        'nv' => q($props['nv']),
        'b_start' => '<strong>',
        'b_end' => '</strong>',
    ]);

    $hasUC = ($props['uc'] && (is_array($props['uc']) || $props['uc']->id != $codepoint->id))? 'u' : '';
    $hasLC = ($props['lc'] && (is_array($props['lc']) || $props['lc']->id != $codepoint->id))? 'l' : '';
    $hasTC = ($props['tc'] && (is_array($props['tc']) || $props['tc']->id != $codepoint->id))? 't' : '';
    echo ' '.
      _f(__('{related_cases, select,
        u {Its uppercase variant is {upper}.}
        ut {Its uppercase variant is {upper} and its titlecase variant is {title}.}
        ul {Its uppercase variant is {upper} and its lowercase variant is {lower}.}
        utl {Its uppercase variant is {upper}, its titlecase variant is {title}, and its lowercase variant is {lower}.}
        t {Its titlecase variant is {title}.}
        tl {Its titlecase variant is {title} and its lowercase variant is {lower}.}
        l {Its lowercase variant is {lower}.}
        other {}
      }'), [
        'related_cases' => "$hasUC$hasTC$hasLC",
        'upper' => ($hasUC)? cp($props['uc'], '', 'min') : '',
        'title' => ($hasTC)? cp($props['tc'], '', 'min') : '',
        'lower' => ($hasLC)? cp($props['lc'], '', 'min') : '',
      ]);

    $info_alias = array_values(array_filter($aliases, function(Array $v) : bool {
        return $v['type'] === 'alias';
    }));
    if (count($info_alias)) {
        $_aliases = '';
        for ($i = 0, $j = count($info_alias); $i < $j; $i++) {
            if ($i > 0) {
                if ($i === $j - 1) {
                    $_aliases .= __(' and ');
                } else {
                    $_aliases .= __(', ');
                }
            }
            $_aliases .= '<em>'.q($info_alias[$i]['alias']).'</em>';
        }
        echo ' ';
        printf(__('The character is also known as %s.'), $_aliases);
    }
?></p>

<!-- glyph -->
<p><?php

    $dm_list = array_map(function(Codepoint $item) : string { return cp($item); }, is_array($props['dm'])? $props['dm'] : [$props['dm']]);

    $has_bmg = (array_key_exists('bmg', $props) &&
        $props['bmg'] &&
        $props['bmg']->id != $codepoint->id);

    echo _f('{dt, select,
      none {The glyph is {b_start}not a composition{b_end}.}
      init {{dm_count, plural,
        =1 {The glyph is an {b_start}{dt_legend}{b_end} version of the glyph {dm_list}.}
        other {The glyph is an {b_start}{dt_legend}{b_end} composition of the glyphs {dm_list}.}
      }}
      iso {{dm_count, plural,
        =1 {The glyph is an {b_start}{dt_legend}{b_end} version of the glyph {dm_list}.}
        other {The glyph is an {b_start}{dt_legend}{b_end} composition of the glyphs {dm_list}.}
      }}
      other {{dm_count, plural,
        =1 {The glyph is a {b_start}{dt_legend}{b_end} version of the glyph {dm_list}.}
        other {The glyph is a {b_start}{dt_legend}{b_end} composition of the glyphs {dm_list}.}
      }}
    }', [
      'dt' => $props['dt'],
      'dt_legend' => q($info->getLegend('dt', $props['dt'])),
      'dm_count' => count($dm_list),
      'dm_list' => join(', ', $dm_list),
      'b_start' => '<strong>',
      'b_end' => '</strong>',
    ]).
    ' '.
    _f('{ea, select,
      N {It has {b_start}no designated width{b_end} in East Asian texts.}
      A {Its width in East Asian texts is {b_start}determined by its context{b_end}. It can be displayed wide or narrow.}
      F {{dt, select,
        wide {Accordingly, its width in East Asian Text is {b_start}{legend}{b_end}.}
        other {Its East Asian Width is {b_start}{legend}{b_end}.}
      }}
      H {{dt, select,
        nar {Accordingly, its width in East Asian Text is {b_start}{legend}{b_end}.}
        other {Its East Asian Width is {b_start}{legend}{b_end}.}
      }}
      other {Its East Asian Width is {b_start}{legend}{b_end}.}
    }', [
        'dt' => $props['dt'],
        'ea' => $props['ea'],
        'b_start' => '<strong>',
        'b_end' => '</strong>',
        'legend' => q($info->getLegend('ea', $props['ea'])),
    ]).
    ' '.
    _f('{bc, select,
        AL {In bidirectional text it is written as Arabic letter {b_start}from right to left{b_end}.}
        AN {In bidirectional text it is written as Arabic number {b_start}from right to left{b_end}.}
        CS {In bidirectional text it is written as number separator {b_start}according to the number it separates{b_end}.}
        EN {In bidirectional text it is written as European number {b_start}from left to right{b_end}.}
        ES {In bidirectional text it is written as European number separator {b_start}from left to right{b_end}.}
        ET {In bidirectional text it is written as end of a European number, e.g., a currency symbol, {b_start}from left to right{b_end}.}
        FSI {In bidirectional text it sets the {b_start}direction{b_end} of the following text snippet based on its first character.}
        L {In bidirectional text it is written {b_start}from left to right{b_end}.}
        LRE {In bidirectional text it marks the following text snippet as {b_start}left-to-right{b_end}.}
        LRI {In bidirectional text it marks the following text as {b_start}isolated left-to-right snippet{b_end}.}
        LRO {In bidirectional text it marks all following text as {b_start}left-to-right{b_end}.}
        PDI {In bidirectional text it clears the effect of {b_start}preceding isolating direction markers{b_end}.}
        PDF {In bidirectional text it clears the effect of {b_start}preceding direction markers{b_end}.}
        R {In bidirectional text it is written {b_start}from right to left{b_end}.}
        RLE {In bidirectional text it marks the following text snippet as {b_start}right-to-left{b_end}.}
        RLI {In bidirectional text it marks the following text as {b_start}isolated right-to-left snippet{b_end}.}
        RLO {In bidirectional text it marks all following text as {b_start}right-to-left{b_end}.}
        other {In bidirectional text it acts as {b_start}{bc_legend}{b_end}.}
    }', [
        'bc' => $props['bc'],
        'bc_legend' => q($info->getLegend('bc', $props['bc'])),
        'b_start' => '<strong>',
        'b_end' => '</strong>',
    ]).
    ' '.
    _f('{Bidi_M, select,
        1 {{bmg, select,
            NONE {When changing direction it is {b_start}mirrored{b_end}.}
            other {When changing direction it is {b_start}mirrored{b_end} into {bmg}.}
          }}
        other {When changing direction it is not {b_start}mirrored{b_end}.}
    }', [
        'Bidi_M' => $props['Bidi_M'],
        'bmg' => $has_bmg? cp($props['bmg'], '', 'min') : 'NONE',
        'b_start' => '<strong>',
        'b_end' => '</strong>',
    ]).
    ' '.
    _f('{lb, select,
        './* TR14 Non-tailorable Line Breaking Classes */'
        BK {{cp} forces a {b_start}line break{b_end} after it.}
        CR {{cp} forces a {b_start}line break{b_end} after it.}
        LF {{cp} forces a {b_start}line break{b_end} after it.}
        NL {{cp} forces a {b_start}line break{b_end} after it.}
        CM {{cp} prohibits a {b_start}line break{b_end} before it.}
        SG {{cp} should never appear in texts that need {b_start}line break{b_end} handling.}
        WJ {{cp} prohibits a {b_start}line break{b_end} around it.}
        ZW {{cp} offers a {b_start}line break{b_end} opportunity at its position.}
        GL {{cp} prohibits a {b_start}line break{b_end} around it.}
        SP {{cp} allows {b_start}line breaks{b_end} at its position.}
        ZWJ {{cp} prohibits a {b_start}line break{b_end} around it.}
        './* TR14 Break Opportunities */'
        B2 {{cp} offers a {b_start}line break{b_end} opportunity at its position.}
        BA {{cp} offers a {b_start}line break{b_end} opportunity after its position.}
        BB {{cp} offers a {b_start}line break{b_end} opportunity before its position.}
        HY {{cp} offers a {b_start}line break{b_end} opportunity after its position unless inside numbers.}
        CB {Depending on the context {cp} offers a {b_start}line break{b_end} opportunity at its position.}
        './* TR14 Characters Prohibiting Certain Breaks */'
        CL {{cp} prohibits a {b_start}line break{b_end} before it.}
        CP {{cp} prohibits a {b_start}line break{b_end} before it.}
        EX {{cp} prohibits a {b_start}line break{b_end} before it.}
        IN {{cp} allows only {b_start}line breaks{b_end}, if an additional space character is present.}
        NS {{cp} prohibits {b_start}line breaks{b_end} before its position. There might be some exceptions, though.}
        OP {{cp} prohibits a {b_start}line break{b_end} after it.}
        QU {{cp} prohibits a {b_start}line break{b_end} around it.}
        './* TR14 Numeric Context */'
        IS {{cp} prohibits a {b_start}line break{b_end} after it, and before it, too, if preceded by a number.}
        NU {{cp} forms a number with similar characters, which prevents a {b_start}line break{b_end} inside it.}
        PO {{cp} prohibits a {b_start}line break{b_end} before it, if it follows a number.}
        PR {{cp} prohibits a {b_start}line break{b_end} after it, if it is followed by a number.}
        SY {{cp} prohibits a {b_start}line break{b_end} before, but allows one after it.}
        './* TR14 Other Characters */'
        AI {If its East Asian Width is “narrow”, {cp} forms a word with similar characters, which prevents a {b_start}line break{b_end} inside it. Otherwise it allows line breaks around it, except in some numeric contexts.}
        AK {{cp} forms an orthographic syllable in Brahmic scripts with similar characters, which prevents a {b_start}line break{b_end} inside it.}
        AL {{cp} forms a word with similar characters, which prevents a {b_start}line break{b_end} inside it.}
        AP {{cp} forms an orthographic syllable in Brahmic scripts with similar characters, which prevents a {b_start}line break{b_end} inside it.}
        AS {{cp} forms an orthographic syllable in Brahmic scripts with similar characters, which prevents a {b_start}line break{b_end} inside it.}
        CJ {{cp} prohibits {b_start}line breaks{b_end} before its position. There are some exceptions, though.}
        EB {{cp} prohibits a {b_start}line break{b_end} after it, if it’s followed by an emoji modifier.}
        EM {{cp} prohibits a {b_start}line break{b_end} before it, if it’s preceded by an emoji base character.}
        H2 {{cp} forms a Korean syllable block with similar characters, which prevents a {b_start}line break{b_end} inside it.}
        H3 {{cp} forms a Korean syllable block with similar characters, which prevents a {b_start}line break{b_end} inside it.}
        HL {{cp} forms a word with similar characters and the hyphen, which prevents a {b_start}line break{b_end} inside it.}
        ID {{cp} offers a {b_start}line break{b_end} opportunity at its position, except in some numeric contexts.}
        JL {{cp} forms a Korean syllable block with similar characters, which prevents a {b_start}line break{b_end} inside it.}
        JV {{cp} forms a Korean syllable block with similar characters, which prevents a {b_start}line break{b_end} inside it.}
        JT {{cp} forms a Korean syllable block with similar characters, which prevents a {b_start}line break{b_end} inside it.}
        RI {{cp} prohibits a {b_start}line break{b_end} in pairs of continuous regional indicator characters.}
        SA {{cp} offers a {b_start}line break{b_end} opportunity at its position depending on the further context.}
        VF {{cp} forms an orthographic syllable in Brahmic scripts with similar characters, which prevents a {b_start}line break{b_end} inside it.}
        VI {{cp} forms an orthographic syllable in Brahmic scripts with similar characters, which prevents a {b_start}line break{b_end} inside it.}
        XX {{cp} does {b_start}not participate{b_end} in line break calculations.}
        other {In text {cp} behaves as {b_start}{lb_legend}{b_end} regarding line breaks.}
    }', [
        'cp' => $codepoint,
        'lb' => $props['lb'],
        'lb_legend' => q($info->getLegend('lb', $props['lb'])),
        'b_start' => '<strong>',
        'b_end' => '</strong>',
    ]).
    ' '.
    _f('{WB, select,
        AL {This letter joins with other adjacent letters and numbers to form a {b_start}word{b_end}.}
        CR {If it is followed by U+000A LINE FEED, these two form a {b_start}non-breaking pair{b_end}.}
        HL {This letter joins with other adjacent letters and numbers to form a {b_start}word{b_end}.}
        KA {This katakana joins with other adjacent katakana to form a {b_start}word{b_end}.}
        NU {This number joins with other adjacent letters and numbers to form a {b_start}word{b_end}.}
        RI {If it is followed by another Regional Indicator character, they form a {b_start}non-breaking pair{b_end}.}
        SQ {In Hebrew text this single quote joins with other Hebrew letters to form a {b_start}word{b_end}.}
        other {}
    }', [
        'WB' => $props['WB'],
        'b_start' => '<strong>',
        'b_end' => '</strong>',
    ]).
    ' '.
    _f('{SB, select,
        AT {It can end {b_start}sentences{b_end} at appropriate places.}
        CR {It can end {b_start}sentences{b_end} at appropriate places, unless followed by U+000A LINE FEED.}
        LF {It can end {b_start}sentences{b_end} at appropriate places.}
        SC {It will not end a {b_start}sentence{b_end}.}
        SE {It can end {b_start}sentences{b_end} at appropriate places.}
        ST {It can end {b_start}sentences{b_end} at appropriate places.}
        other {}
    }', [
        'SB' => $props['SB'],
        'b_start' => '<strong>',
        'b_end' => '</strong>',
    ]).
    ' '.
    _f('{confusables, plural,
        =0 {}
        =1 {The glyph can be confused with {link_start}one other glyph{link_end}.}
        other {The glyph can be confused with {link_start}{confusables, number} other glyphs{link_end}.}
    }', [
        'link_start' => '<a href="#confusables" rel="internal">',
        'confusables' => count($confusables),
        'link_end' => '</a>',
    ]);
?></p>

<?php if ($codepoint->cldr['tts']): ?>
<p>
  <?=sprintf(_q('The %sCLDR project%s calls this character “%s” for use in screen reading software.'),
      '<a href="https://cldr.unicode.org/">', '</a>', $codepoint->cldr['tts'])?>
  <?php if ($codepoint->cldr['tags']): ?>
  <?=' '.sprintf(_q('It assigns these additional labels, e.g. for search in emoji pickers: %s.'),
     q(join(', ', $codepoint->cldr['tags'])))?>
  <?php endif ?>
</p>
<?php endif ?>

<?php if ($props['Emoji']): ?>
<p>
  <?=_q('This character is designated as an emoji.')?>
  <?php if ($props['EPres']): ?>
    <?=_q('It will be rendered as colorful emoji on conforming platforms.')?>
    <?=sprintf(_q('To reduce it to a monochrome character, you can combine it with %s: %s'), cp($vs15), $codepoint->chr()."\u{FE0E}")?>
  <?php else: ?>
    <?=_q('It will be rendered as monochrome character on conforming platforms.')?>
    <?=sprintf(_q('To enable colorful emoji display, you can combine it with %s: %s'), cp($vs16), $codepoint->chr()."\u{FE0F}")?>
  <?php endif ?>
  <?php if ($props['EBase']): ?>
    <?=sprintf(_q('The character can be changed in appearance, if it is followed by %san emoji modifier%s.'), '<a href="'.url('search?EMod=1').'">', '</a>')?>
  <?php endif ?>
  <?=sprintf(_q('See the %sEmojipedia%s for more details on this character’s emoji properties.'), '<a href="https://emojipedia.org/'.rawurlencode($codepoint->chr()).'">', '</a>')?>
</p>
<?php endif ?>
