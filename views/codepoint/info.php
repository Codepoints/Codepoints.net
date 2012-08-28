<!-- codepoint -->
<p>
  <?php $plane = $codepoint->getPlane();
  printf(__('U+%04X was added to Unicode in version %s. It belongs to the block %s in the %s.'),
    $codepoint->getId(),
    '<a href="'.q($router->getUrl('search?age='.$props['age'])).'">'.
    q($info->getLabel('age', $props['age'])).'</a>',
    _bl($block),
    '<a class="pl" href="'.q($router->getUrl($plane)).'">'.q($plane->name).'</a>');
  if ($props['Dep']):
    printf(__('This codepoint is %sdeprecated%s.'),
      '<a href="'.q($router->getUrl('search?Dep=1')).'">', '</a>');
  endif?>
</p>

<!-- character -->
<p>
  This character is a <?php $s('gc')?> and
  <?php if ($props['sc'] === 'Zyyy'):?>
    is <a href="<?php e($router->getUrl('search?sc='.$props['sc']))?>">commonly</a>
    used, that is, in no specific script.
  <?php elseif ($props['sc'] === 'Zinh'):?>
    <a href="<?php e($router->getUrl('search?sc='.$props['sc']))?>">inherits</a>
    its <span class="gl" data-term="sc">script property</span> from the
    preceding character.
  <?php else:?>
    is mainly used in the <?php $s('sc')?> script.
  <?php endif?>
  <?php $buf=array(); foreach(explode(' ', $props['scx']) as $sc): if ($sc !== $props['sc']):
    $buf[] = '<a href="'.q($router->getUrl('search?scx='.$props['scx'])).'">'.
              q($info->getLabel('sc', $sc)).'</a>';
  endif; endforeach;
  if (count($buf)):?>
    It is also used in the script<?php if (count($buf) > 1):?>s<?php endif?>
    <?php echo join(', ', $buf)?>.
  <?php endif?>

  <?php $defn = $codepoint->getProp('kDefinition');
    if ($defn):
    printf(__('The Unihan Database defines it as <em>%s</em>.'),
      preg_replace_callback('/U\+([0-9A-F]{4,6})/', function($m) {
          $router = Router::getRouter();
          $db = $router->getSetting('db');
          return _cp(Codepoint::getCP(hexdec($m[1]), $db), '', 'min');
      }, $defn));
  endif?>
  <?php $pronunciation = $codepoint->getPronunciation();
  if ($pronunciation):
    printf(__('Its Pīnyīn pronunciation is <em>%s</em>.'), q($pronunciation));
  endif?>

  <?php if($props['nt'] !== 'None'):
    printf(__('The codepoint has the %s value %s.'),
    '<a href="'.q($router->getUrl('search?nt='.$props['nt'])).'">'.
    q($info->getLabel('nt', $props['nt'])).'</a>',
    '<a href="'.q($router->getUrl('search?nv='.$props['nv'])).'">'.
    q($info->getLabel('nv', $props['nv'])).'</a>');
  endif?>

  <?php
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
  <?php endif?>
  <?php $info_alias = array_values(array_filter($codepoint->getALias(), function($v) {
        return $v['type'] === 'alias';
  })); if (count($info_alias)): ?>
    The character is also known as
    <?php for ($i = 0, $j = count($info_alias); $i < $j; $i++):?><?php if ($i > 0): if ($i === $j - 1):?> and <?php else: ?>, <?php endif; endif?>
      <em><?php e($info_alias[$i]['alias'])?></em><?php endfor?>.
  <?php endif?>
</p>

<!-- glyph -->
<p>
  The glyph is
  <?php if ($props['dt'] === 'none'):?>
    <a href="<?php e($router->getUrl('search?dt=none'))?>">not a
    composition</a>.
  <?php else:?>
    a <?php $s('dt')?> composition of the glyphs
    <?php cp($props['dm'], '', 'min')?>.
  <?php endif?>
  It has a <?php $s('ea')?> <?php e($info->getCategory('ea'))?>.

  In bidirectional context it acts as <?php $s('bc')?>
  and is <a href="<?php e($router->getUrl('search?bc='.$props['bc'].'&bm='.
         (int)$props['Bidi_M']))?>"><?php
  if (! $props['Bidi_M']):?>not <?php endif?>mirrored</a>.
  <?php if (array_key_exists('bmg', $props) &&
            $props['bmg']->getId() != $codepoint->getId()):
    printf(__('Its corresponding mirrored glyph is %s.'), _cp($props['bmg'], '', 'min'));
  endif?>

  <?php if (count($confusables)):?>
    <?php printf(__('The glyph can, under circumstances, be confused with %s%d other glyphs%s.'),
    '<a href="#confusables" rel="internal">', count($confusables), '</a>')?>
  <?php endif?>

  <?php printf(__('In text U+%04X behaves as %s regarding line breaks. It has
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
    q($info->getLabel('GCB', $props['GCB'])).'</a>')?>
</p>

<!-- Wikipedia -->
<?php if (array_key_exists('abstract', $props) && $props['abstract']):?>
  <p><?php printf(__('The %sWikipedia%s has the following information about this codepoint:'), '<a href="http://en.wikipedia.org/wiki/%'.q($codepoint->getRepr('UTF-8', '%')).'">', '</a>')?></p>
  <blockquote cite="http://en.wikipedia.org/wiki/%<?php e($codepoint->getRepr('UTF-8', '%'))?>">
    <?php echo strip_tags($props['abstract'], '<p><b><strong class="selflink"><strong><em><i><var><sup><sub><tt><ul><ol><li><samp><small><hr><h2><h3><h4><h5><dfn><dl><dd><dt><u><abbr><big><blockquote><br><center><del><ins><kbd>')?>
  </blockquote>
<?php endif?>

