<?php
$title = 'Block ' . $block->getName();
include "header.php";
$bounds = $block->getBoundaries();
$prev = $block->getPrev();
$next = $block->getNext();
$plane = $block->getPlane();
$pagination = new Pagination(count($block->get()));
$page = isset($_GET['page'])? intval($_GET['page']) : 1;
$pagination->setPage($page);
?>
  <h1><?php e($title);?></h1>
  <p>From U+<?php f('%04X', $bounds[0])?>
     to U+<?php f('%04X', $bounds[1])?></p>
  <dl>
    <?php if ($prev):?>
      <dt>Previous</dt>
      <dd><a class="bl" href="<?php e(str_replace(' ', '_', strtolower($prev->getName())))?>"><?php e($prev->getName())?></a></dd>
    <?php endif?>
    <?php if ($next):?>
      <dt>Next</dt>
      <dd><a class="bl" href="<?php e(str_replace(' ', '_', strtolower($next->getName())))?>"><?php e($next->getName())?></a></dd>
    <?php endif?>
    <dt>Plane</dt>
    <dd><a class="pl" href="<?php e(str_replace(' ', '_', strtolower($plane->getName())))?>"><?php e($plane->getName())?></a></dd>
  </dl>
  <?php echo $pagination?>
  <ol class="block">
    <?php foreach ($pagination->getSet($block->get()) as $cp => $na):
      f('<li value="%s"><a class="cp" href="U+%04X" title="%s">%04X<img src="data:%s" alt="" width="16" height="16" /></a></li>',
              $cp, $cp, $na, $cp, $na->getImage());
    endforeach ?>
  </ol>
  <?php echo $pagination?>
<?php include "footer.php"?>
