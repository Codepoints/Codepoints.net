<?php
$title = 'Block ' . $block->getName();
include "header.php";
$bounds = $block->getBoundaries();
$prev = $block->getPrev();
$next = $block->getNext();
$plane = $block->getPlane();
?>
  <h1><?php echo $title;?></h1>
  <p>From U+<?php printf('%04X', $bounds[0])?>
     to U+<?php printf('%04X', $bounds[1])?></p>
  <dl>
    <?php if ($prev):?>
      <dt>Previous</dt>
      <dd><a class="bl" href="<?php echo str_replace(' ', '_', strtolower($prev->getName()))?>"><?php echo $prev->getName()?></a></dd>
    <?php endif?>
    <?php if ($next):?>
      <dt>Next</dt>
      <dd><a class="bl" href="<?php echo str_replace(' ', '_', strtolower($next->getName()))?>"><?php echo $next->getName()?></a></dd>
    <?php endif?>
    <dt>Plane</dt>
    <dd><a class="pl" href="<?php echo str_replace(' ', '_', strtolower($plane->getName()))?>"><?php echo $plane->getName()?></a></dd>
  </dl>
  <ol class="block">
    <?php foreach ($block as $cp => $na):
      printf('<li value="%s"><a class="cp" href="U+%04X" title="%s">%04X<img src="data:%s" alt="" width="16" height="16" /></a></li>',
              $cp, $cp, $na, $cp, $na->getImage());
    endforeach ?>
  </ol>
<?php include "footer.php"?>
