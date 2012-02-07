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
      <dd><a href="<?php echo str_replace(' ', '_', strtolower($prev->getName()))?>"><?php echo $prev->getName()?></a></dd>
    <?php endif?>
    <?php if ($next):?>
      <dt>Next</dt>
      <dd><a href="<?php echo str_replace(' ', '_', strtolower($next->getName()))?>"><?php echo $next->getName()?></a></dd>
    <?php endif?>
    <dt>Plane</dt>
    <dd><a href="<?php echo str_replace(' ', '_', strtolower($plane->getName()))?>"><?php echo $plane->getName()?></a></dd>
  </dl>
  <ol>
    <?php foreach ($block->getSetNames() as $cp => $na):
      printf('<li value="%s"><a href="U+%04X">U+%04X, %s</a></li>',
              $cp, $cp, $cp, $na);
    endforeach ?>
  </ol>
<?php include "footer.php"?>
