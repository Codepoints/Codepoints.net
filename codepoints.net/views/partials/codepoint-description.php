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
    printf(__('U+%04X was added to Unicode in version %s (%s). It belongs to the block %s in the %s.'),
        $codepoint->id,
        '<a rel="nofollow" href="'.q(url('search?age='.$props['age'])).'">'.
        q($props['age']).'</a>',
        array_get($info->age_to_year, $props['age'], $props['age']),
        $block? bl($block, 'up'):'-',
        pl($plane, 'up'));

    if ($props['Dep']) {
        echo ' ';
        printf(__('This codepoint is %sdeprecated%s.'),
        '<a rel="nofollow" href="'.q(url('search?Dep=1')).'">', '</a>');
    }
?></p>

<!-- character -->
<p><?php
    if ($props['sc'] === 'Zyyy') {
        printf(__('This character is a %s and is %scommonly%s used, that is, in no specific script.'),
            '<a rel="nofollow" href="'.q(url('search?gc='.$props['gc'])).'">'.
            q($info->getLegend('gc', $props['gc'])).'</a>',
            '<a rel="nofollow" href="'.q(url('search?sc='.$props['sc'])).'">',
            '</a>');
    } elseif ($props['sc'] === 'Zinh') {
        printf(__('This character is a %s and %sinherits%s its %sscript property%s from the preceding character.'),
            '<a rel="nofollow" href="'.q(url('search?gc='.$props['gc'])).'">'.
            q($info->getLegend('gc', $props['gc'])).'</a>',
            '<a rel="nofollow" href="'.q(url('search?sc='.$props['sc'])).'">',
            '</a>',
            '<span class="gl" data-term="sc">',
            '</span>');
    } else {
        printf(__('This character is a %s and is mainly used in the %s script.'),
            '<a rel="nofollow" href="'.q(url('search?gc='.$props['gc'])).'">'.
            q($info->getLegend('gc', $props['gc'])).'</a>',
            '<a rel="nofollow" href="'.q(url('search?sc='.$props['sc'])).'">'.
            q(array_get($info->script, $props['sc'], $props['sc'])).'</a>');
    }

    $buf = [];
    foreach($props['scx'] as $sc) {
        if (array_key_exists('sc', $props) && $sc !== $props['sc']) {
            $buf[] = '<a rel="nofollow" href="'.q(url('search?sc='.$sc)).'">'.
                    q(array_get($info->script, $sc, $sc)).'</a>';
        }
    }

    if (count($buf)) {
        echo ' ';
        printf(__('It is also used in the script%s %s.'),
            (count($buf) > 1)? 's' : '',
            join(', ', $buf));
    }

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

    if($props['nt'] !== 'None') {
        echo ' ';
        printf(__('The codepoint has the %s value %s.'),
        '<a rel="nofollow" href="'.q(url('search?nt='.$props['nt'])).'">'.
        q($info->getLegend('nt', $props['nt'])).'</a>',
        '<a rel="nofollow" href="'.q(url('search?nv='.$props['nv'])).'">'.
        q($props['nv']).'</a>');
    }

    $hasUC = ($props['uc'] && (is_array($props['uc']) || $props['uc']->id != $codepoint->id));
    $hasLC = ($props['lc'] && (is_array($props['lc']) || $props['lc']->id != $codepoint->id));
    $hasTC = ($props['tc'] && (is_array($props['tc']) || $props['tc']->id != $codepoint->id));
    echo ' ';
    if ($hasUC || $hasLC || $hasTC) {
        printf(__('It is related to %s%s%s%s%s.'),
            ($hasUC)? sprintf(__('its uppercase variant %s'), cp($props['uc'], '', 'min')) : '',
            ($hasLC && $hasUC)? (($hasTC)? __(', ') : __(' and ')) : '',
            ($hasLC)? sprintf(__('its lowercase variant %s'), cp($props['lc'], '', 'min')) : '',
            ($hasTC && ($hasUC || $hasLC))? __(' and ') : '',
            ($hasTC)? sprintf(__('its titlecase variant %s'), cp($props['tc'], '', 'min')) : ''
        );
    }

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

    if ($props['dt'] === 'none') {
        printf(__('The glyph is %snot a composition%s.'),
            '<a rel="nofollow" href="'.q(url('search?dt=none')).'">',
            '</a>');
    } elseif (! is_array($props['dm'])) {
        printf(__('The glyph is a %s composition of the glyph %s.'),
            '<a rel="nofollow" href="'.q(url('search?dt='.$props['dt'])).'">'.
            q($info->getLegend('dt', $props['dt'])).'</a>',
            cp($props['dm']));
    } else {
        printf(__('The glyph is a %s composition of the glyphs %s.'),
            '<a rel="nofollow" href="'.q(url('search?dt='.$props['dt'])).'">'.
            q($info->getLegend('dt', $props['dt'])).'</a>',
            join(', ', array_map(function(Codepoint $item) : string { return cp($item); }, $props['dm'])));
    }

    echo ' ';
    printf(__('It has a %s %s.'),
        '<a rel="nofollow" href="'.q(url('search?ea='.$props['ea'])).'">'.
        q($info->getLegend('ea', $props['ea'])).'</a>',
        q($info->properties['ea']));

    if ($props['Bidi_M']) {
        echo ' ';
        printf(__('In bidirectional context it acts as %s and is %smirrored%s.'),
            '<a rel="nofollow" href="'.q(url('search?bc='.$props['bc'])).'">'.
            q($info->getLegend('bc', $props['bc'])).'</a>',
            '<a rel="nofollow" href="'.q(url('search?bc='.$props['bc'].'&bm='.
            (int)$props['Bidi_M'])).'">',
            '</a>'
        );
    } else {
        echo ' ';
        printf(__('In bidirectional context it acts as %s and is %snot mirrored%s.'),
            '<a rel="nofollow" href="'.q(url('search?bc='.$props['bc'])).'">'.
            q($info->getLegend('bc', $props['bc'])).'</a>',
            '<a rel="nofollow" href="'.q(url('search?bc='.$props['bc'].'&bm='.
            (int)$props['Bidi_M'])).'">',
            '</a>'
        );
    }

    if (array_key_exists('bmg', $props) &&
        $props['bmg'] &&
        $props['bmg']->id != $codepoint->id) {
        echo ' ';
        printf(__('Its corresponding mirrored glyph is %s.'), cp($props['bmg'], '', 'min'));
    }

    if (count($confusables)) {
        echo ' ';
        printf(__('The glyph can, under circumstances, be confused with %s%d other glyphs%s.'),
               '<a href="#confusables" rel="internal">', count($confusables), '</a>');
    }

    echo ' ';
    printf(__('In text U+%04X behaves as %s regarding line breaks.'),
        $codepoint->id,
        '<a rel="nofollow" href="'.q(url('search?lb='.$props['lb'])).'">'.
        q($info->getLegend('lb', $props['lb'])).'</a>');
    echo ' ';
    printf(__('It has type %s for sentence and %s for word breaks.'),
        '<a rel="nofollow" href="'.q(url('search?SB='.$props['SB'])).'">'.
        q($info->getLegend('SB', $props['SB'])).'</a>',
        '<a rel="nofollow" href="'.q(url('search?WB='.$props['WB'])).'">'.
        q($info->getLegend('WB', $props['WB'])).'</a>');
    echo ' ';
    printf(__('The %s is %s.'),
        q($info->properties['GCB']),
        '<a rel="nofollow" href="'.q(url('search?GCB='.$props['GCB'])).'">'.
        q($info->getLegend('GCB', $props['GCB'])).'</a>');
?></p>

<?php if ($codepoint->cldr['tts']): ?>
<p>
  <?=sprintf(_q('The %sCLDR project%s labels this character “%s” for use in screen reading software.'),
      '<a href="http://cldr.unicode.org/">', '</a>', $codepoint->cldr['tts'])?>
  <?php if ($codepoint->cldr['tags']): ?>
  <?=' '.sprintf(_q('It assigns additional tags, e.g. for search in emoji pickers: %s.'),
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
  <?=sprintf(_q('See %sthe Emojipedia%s for more details on this character’s emoji properties.'), '<a href="https://emojipedia.org/'.rawurlencode($codepoint->chr()).'">', '</a>')?>
</p>
<?php endif ?>
