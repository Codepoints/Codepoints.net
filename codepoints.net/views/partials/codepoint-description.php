<!-- codepoint -->
<p><?php
    printf(__('U+%04X was added to Unicode in version %s (%s). It belongs to the block %s in the %s.'),
        $codepoint->id,
        '<a rel="nofollow" href="'.q(url('search?age='.$props['age'])).'">'.
        q($props['age']).'</a>',
        array_get($info->age_to_year, $props['age'], $props['age']),
        $block? bl($block):'-',
        pl($plane));

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
            q(array_get($info->legend_gc, $props['gc'], $props['gc'])).'</a>',
            '<a rel="nofollow" href="'.q(url('search?sc='.$props['sc'])).'">',
            '</a>');
    } elseif ($props['sc'] === 'Zinh') {
        printf(__('This character is a %s and %sinherits%s its %sscript property%s from the preceding character.'),
            '<a rel="nofollow" href="'.q(url('search?gc='.$props['gc'])).'">'.
            q(array_get($info->legend_gc, $props['gc'], $props['gc'])).'</a>',
            '<a rel="nofollow" href="'.q(url('search?sc='.$props['sc'])).'">',
            '</a>',
            '<span class="gl" data-term="sc">',
            '</span>');
    } else {
        printf(__('This character is a %s and is mainly used in the %s script.'),
            '<a rel="nofollow" href="'.q(url('search?gc='.$props['gc'])).'">'.
            q(array_get($info->legend_gc, $props['gc'], $props['gc'])).'</a>',
            '<a rel="nofollow" href="'.q(url('search?sc='.$props['sc'])).'">'.
            q(array_get($info->script, $props['sc'], $props['sc'])).'</a>');
    }

    $buf = [];
    foreach(explode(' ', $props['scx']) as $sc) {
        if ($sc !== $props['sc']) {
            $buf[] = '<a rel="nofollow" href="'.q(url('search?scx='.$props['scx'])).'">'.
                    q(array_get($info->script, $sc, $sc)).'</a>';
        }
    }

    if (count($buf)) {
        echo ' ';
        printf(__('It is also used in the script%s %s.'),
            (count($buf) > 1)? 's' : '',
            join(', ', $buf));
    }

    $defn = $codepoint->properties['kDefinition'];
    if ($defn) {
        echo ' ';
        printf(__('The Unihan Database defines it as <em>%s</em>.'),
            preg_replace_callback('/U\+([0-9A-F]{4,6})/', function(Array $m) : string {
                # TODO
                return $m[0];
                #$router = Router::getRouter();
                #$db = $router->getSetting('db');
                #return cp(Codepoint::getCP(hexdec($m[1]), $db), '', 'min');
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
        q(array_get($info->legend_nt, $props['nt'], $props['nt'])).'</a>',
        '<a rel="nofollow" href="'.q(url('search?nv='.$props['nv'])).'">'.
        q(array_get($info->legend_nv, $props['nv'], $props['nv'])).'</a>');
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
    } else {
        printf(__('The glyph is a %s composition of the glyphs %s.'),
            '<a rel="nofollow" href="'.q(url('search?dt='.$props['dt'])).'">'.
            q(array_get($info->legend_dt, $props['dt'], $props['dt'])).'</a>',
            cp($props['dm'], ''));
    }

    echo ' ';
    printf(__('It has a %s %s.'),
        '<a rel="nofollow" href="'.q(url('search?ea='.$props['ea'])).'">'.
        q(array_get($info->legend_ea, $props['ea'], $props['ea'])).'</a>',
        q($info->properties['ea']));

    if ($props['Bidi_M']) {
        echo ' ';
        printf(__('In bidirectional context it acts as %s and is %smirrored%s.'),
            '<a rel="nofollow" href="'.q(url('search?bc='.$props['bc'])).'">'.
            q(array_get($info->legend_bc, $props['bc'], $props['bc'])).'</a>',
            '<a rel="nofollow" href="'.q(url('search?bc='.$props['bc'].'&bm='.
            (int)$props['Bidi_M'])).'">',
            '</a>'
        );
    } else {
        echo ' ';
        printf(__('In bidirectional context it acts as %s and is %snot mirrored%s.'),
            '<a rel="nofollow" href="'.q(url('search?bc='.$props['bc'])).'">'.
            q(array_get($info->legend_bc, $props['bc'], $props['bc'])).'</a>',
            '<a rel="nofollow" href="'.q(url('search?bc='.$props['bc'].'&bm='.
            (int)$props['Bidi_M'])).'">',
            '</a>'
        );
    }

    if (array_key_exists('bmg', $props) &&
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
    printf(__('In text U+%04X behaves as %s regarding line breaks. It has
        type %s for sentence and %s for word breaks. The %s is %s.'),
        $codepoint->id,
        '<a rel="nofollow" href="'.q(url('search?lb='.$props['lb'])).'">'.
        q(array_get($info->legend_lb, $props['lb'], [$props['lb']])[0]).'</a>',
        '<a rel="nofollow" href="'.q(url('search?SB='.$props['SB'])).'">'.
        q(array_get($info->legend_SB, $props['SB'], $props['SB'])).'</a>',
        '<a rel="nofollow" href="'.q(url('search?WB='.$props['WB'])).'">'.
        q(array_get($info->legend_WB, $props['WB'], $props['WB'])).'</a>',
            q($info->properties['GCB']),
        '<a rel="nofollow" href="'.q(url('search?GCB='.$props['GCB'])).'">'.
        q(array_get($info->legend_GCB, $props['GCB'], $props['GCB'])).'</a>');
?></p>
