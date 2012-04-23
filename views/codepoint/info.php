<!-- codepoint -->
<p>
  U+<?php e($codepoint->getId('hex'))?> was added to Unicode in version
  <?php $s('age')?>. It belongs to the block <?php bl($block)?> in the
  <?php $plane = $codepoint->getPlane();
  f('<a class="pl" href="%s">%s</a>',
    $router->getUrl($plane), $plane->name); ?>.
</p>

<!-- character -->
<p>
  This character is a <?php $s('gc')?> and is
  <?php if ($props['sc'] === 'Zyyy'):?>
    <a href="<?php e($router->getUrl('search?sc='.$props['sc']))?>">commonly</a> used.
  <?php else:?>
    mainly used in the <?php $s('sc')?> script.
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
    if ($defn):?>
    The Unihan Database defines it as <em><?php
      echo preg_replace_callback('/U\+([0-9A-F]{4,6})/', function($m) {
          $router = Router::getRouter();
          $db = $router->getSetting('db');
          return _cp(Codepoint::getCP(hexdec($m[1]), $db), '', 'min');
      }, $defn);
    ?></em>.
  <?php endif?>
  <?php $pronunciation = $codepoint->getPronunciation();
    if ($pronunciation):?>
    Its Pīnyīn pronunciation is
    <em><?php e($pronunciation)?></em>.
  <?php endif?>

  <?php if($props['nt'] !== 'None'):?>
    The codepoint has the <?php $s('nt')?> value <?php $s('nv')?>.
  <?php endif?>

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
            $props['bmg']->getId() != $codepoint->getId()):?>
  Its corresponding mirrored glyph is <?php cp($props['bmg'], '', 'min')?>.
  <?php endif?>

  <?php if (count($confusables)):?>
    The glyph can, under circumstances, be confused with
    <a href="#confusables" rel="internal"><?php e(count($confusables))?> other glyphs</a>.
  <?php endif?>

  In text U+<?php e($codepoint->getId('hex'))?> behaves as <?php $s('lb')?> 
  regarding line breaks. It has type <?php $s('SB')?> for sentence and <?php 
  $s('WB')?> for word breaks. The <?php e($info->getCategory('GCB'))?> property 
  is <?php $s('GCB')?>.
</p>

<!-- Wikipedia -->
<?php if (array_key_exists('abstract', $props) && $props['abstract']):?>
  <p>The <a href="http://en.wikipedia.org/wiki/%<?php e($codepoint->getRepr('UTF-8', '%'))?>">Wikipedia</a>
  has the following information about this codepoint:</p>
  <blockquote cite="http://en.wikipedia.org/wiki/%<?php e($codepoint->getRepr('UTF-8', '%'))?>">
    <?php echo strip_tags($props['abstract'], '<p><b><strong class="selflink"><strong><em><i><var><sup><sub><tt><ul><ol><li><samp><small><hr><h2><h3><h4><h5><dfn><dl><dd><dt><u><abbr><big><blockquote><br><center><del><ins><kbd>')?>
  </blockquote>
<?php endif?>

