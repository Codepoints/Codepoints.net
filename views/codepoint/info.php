<!-- codepoint -->
<p><?php
    $plane = $codepoint->getPlane();
    printf(__('U+%04X was added to Unicode in version %s. It belongs to the block %s in the %s.'),
        $codepoint->getId(),
        '<a href="'.q($router->getUrl('search?age='.$props['age'])).'">'.
        q($info->getLabel('age', $props['age'])).'</a>',
        _bl($block),
        '<a class="pl" href="'.q($router->getUrl($plane)).'">'.q($plane->name).'</a>');

    if ($props['Dep']) {
        printf(__('This codepoint is %sdeprecated%s.'),
        '<a href="'.q($router->getUrl('search?Dep=1')).'">', '</a>');
    }
?></p>

<!-- character -->
<p><?php
    if ($props['sc'] === 'Zyyy') {
        printf(__('This character is a %s and is %scommonly%s used, that is, in no specific script.'),
            '<a href="'.q($router->getUrl('search?gc='.$props['gc'])).'">'.
            q($info->getLabel('gc', $props['gc'])).'</a>',
            '<a href="'.q($router->getUrl('search?sc='.$props['sc'])).'">',
            '</a>');
    } elseif ($props['sc'] === 'Zinh') {
        printf(__('This character is a %s and %sinherits%s its %sscript property%s from the preceding character.'),
            '<a href="'.q($router->getUrl('search?gc='.$props['gc'])).'">'.
            q($info->getLabel('gc', $props['gc'])).'</a>',
            '<a href="'.q($router->getUrl('search?sc='.$props['sc'])).'">',
            '</a>',
            '<span class="gl" data-term="sc">',
            '</span>');
    } else {
        printf(__('This character is a %s and is mainly used in the %s script.'),
            '<a href="'.q($router->getUrl('search?gc='.$props['gc'])).'">'.
            q($info->getLabel('gc', $props['gc'])).'</a>',
            '<a href="'.q($router->getUrl('search?sc='.$props['sc'])).'">'.
            q($info->getLabel('sc', $props['sc'])).'</a>');
    }

    $buf=array();
    foreach(explode(' ', $props['scx']) as $sc) {
        if ($sc !== $props['sc']) {
            $buf[] = '<a href="'.q($router->getUrl('search?scx='.$props['scx'])).'">'.
                    q($info->getLabel('sc', $sc)).'</a>';
        }
    }

    if (count($buf)) {
        printf(__('It is also used in the script%s %s.'),
            (count($buf) > 1)? 's' : '',
            join(', ', $buf));
    }

    $defn = $codepoint->getProp('kDefinition');
    if ($defn) {
        printf(__('The Unihan Database defines it as <em>%s</em>.'),
            preg_replace_callback('/U\+([0-9A-F]{4,6})/', function($m) {
                $router = Router::getRouter();
                $db = $router->getSetting('db');
                return _cp(Codepoint::getCP(hexdec($m[1]), $db), '', 'min');
            }, $defn));
    }

    $pronunciation = $codepoint->getPronunciation();
    if ($pronunciation) {
        printf(__('Its Pīnyīn pronunciation is <em>%s</em>.'), q($pronunciation));
    }

    if($props['nt'] !== 'None') {
        printf(__('The codepoint has the %s value %s.'),
        '<a href="'.q($router->getUrl('search?nt='.$props['nt'])).'">'.
        q($info->getLabel('nt', $props['nt'])).'</a>',
        '<a href="'.q($router->getUrl('search?nv='.$props['nv'])).'">'.
        q($info->getLabel('nv', $props['nv'])).'</a>');
    }

  $hasUC = ($props['uc'] && (is_array($props['uc']) || $props['uc']->getId() != $codepoint->getId()));
  $hasLC = ($props['lc'] && (is_array($props['lc']) || $props['lc']->getId() != $codepoint->getId()));
  $hasTC = ($props['tc'] && (is_array($props['tc']) || $props['tc']->getId() != $codepoint->getId()));
  if ($hasUC || $hasLC || $hasTC):?>
    It is related to
    <?php if ($hasUC):?>its uppercase variant <?php cp($props['uc'], '', 'min')?><?php endif?>
    <?php if ($hasLC): if ($hasUC) { echo $hasTC? ', ' : ' and '; }?>
      its lowercase variant <?php cp($props['lc'], '', 'min')?><?php endif?>
    <?php if ($hasTC): if ($hasUC || $hasLC) { echo ' and '; }?>
      its titlecase variant <?php cp($props['tc'], '', 'min')?><?php endif?>.
<?php endif;

    $info_alias = array_values(array_filter($codepoint->getALias(), function($v) {
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
        printf(__('The character is also known as %s.'), $_aliases);
    }
?></p>

<!-- glyph -->
<p><?php

    if ($props['dt'] === 'none') {
        printf(__('The glyph is %snot a composition%s.'),
            '<a href="'.q($router->getUrl('search?dt=none')).'">',
            '</a>');
    } else {
        printf(__('The glyph is a %s composition of the glyphs %s.'),
            '<a href="'.q($router->getUrl('search?dt='.$props['dt'])).'">'.
            q($info->getLabel('dt', $props['dt'])).'</a>',
            _cp($props['dm'], '', 'min'));
    }

    printf(__('It has a %s %s.'),
        '<a href="'.q($router->getUrl('search?ea='.$props['ea'])).'">'.
        q($info->getLabel('ea', $props['ea'])).'</a>',
        q($info->getCategory('ea')));

    if ($props['Bidi_M']) {
        printf(__('In bidirectional context it acts as %s and is %smirrored%s.'),
            '<a href="'.q($router->getUrl('search?bc='.$props['bc'])).'">'.
            q($info->getLabel('bc', $props['bc'])).'</a>',
            '<a href="'.q($router->getUrl('search?bc='.$props['bc'].'&bm='.
            (int)$props['Bidi_M'])).'">',
            '</a>'
        );
    } else {
        printf(__('In bidirectional context it acts as %s and is %snot mirrored%s.'),
            '<a href="'.q($router->getUrl('search?bc='.$props['bc'])).'">'.
            q($info->getLabel('bc', $props['bc'])).'</a>',
            '<a href="'.q($router->getUrl('search?bc='.$props['bc'].'&bm='.
            (int)$props['Bidi_M'])).'">',
            '</a>'
        );
    }

    if (array_key_exists('bmg', $props) &&
        $props['bmg']->getId() != $codepoint->getId()) {
        printf(__('Its corresponding mirrored glyph is %s.'), _cp($props['bmg'], '', 'min'));
    }

    if (count($confusables)) {
        printf(__('The glyph can, under circumstances, be confused with %s%d other glyphs%s.'),
               '<a href="#confusables" rel="internal">', count($confusables), '</a>');
    }

    printf(__('In text U+%04X behaves as %s regarding line breaks. It has
        type %s for sentence and %s for word breaks. The %s is %s.'),
        $codepoint->getId(),
        '<a href="'.q($router->getUrl('search?lb='.$props['lb'])).'">'.
        q($info->getLabel('lb', $props['lb'])).'</a>',
        '<a href="'.q($router->getUrl('search?SB='.$props['SB'])).'">'.
        q($info->getLabel('SB', $props['SB'])).'</a>',
        '<a href="'.q($router->getUrl('search?WB='.$props['WB'])).'">'.
        q($info->getLabel('WB', $props['WB'])).'</a>',
            q($info->getCategory('GCB')),
        '<a href="'.q($router->getUrl('search?GCB='.$props['GCB'])).'">'.
        q($info->getLabel('GCB', $props['GCB'])).'</a>');
?></p>

<!-- Wikipedia -->
<?php if (array_key_exists('abstract', $props) && $props['abstract']):?>
  <p><?php printf(__('The %sWikipedia%s has the following information about this codepoint:'), '<a href="http://en.wikipedia.org/wiki/%'.q($codepoint->getRepr('UTF-8', '%')).'">', '</a>')?></p>
  <blockquote cite="http://en.wikipedia.org/wiki/%<?php e($codepoint->getRepr('UTF-8', '%'))?>">
    <?php echo strip_tags($props['abstract'], '<p><b><strong class="selflink"><strong><em><i><var><sup><sub><tt><ul><ol><li><samp><small><hr><h2><h3><h4><h5><dfn><dl><dd><dt><u><abbr><big><blockquote><br><center><del><ins><kbd>')?>
  </blockquote>
<?php endif?>

