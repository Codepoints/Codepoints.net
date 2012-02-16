<?php
$title = $plane->getName();
$blocks = $plane->getBlocks();
$prev = $plane->getPrev();
$next = $plane->getNext();
include "header.php";
?>
  <h1><?php e($title);?></h1>
  <p>From U+<?php f('%04X', $plane->first)?>
     to U+<?php f('%04X', $plane->last)?></p>
  <dl>
    <?php if ($prev):?>
      <dt>Previous</dt>
      <dd><a href="<?php e(str_replace(' ', '_', strtolower($prev->name)))?>"><?php e($prev->name)?></a></dd>
    <?php endif?>
    <?php if ($next):?>
      <dt>Next</dt>
      <dd><a href="<?php e(str_replace(' ', '_', strtolower($next->name)))?>"><?php e($next->name)?></a></dd>
    <?php endif?>
  </dl>
  <h2>Blocks in this plane</h2>
  <ol>
    <?php foreach ($blocks as $b):?>
      <li><a href="<?php e(strtolower(u($b['name'])))?>"><?php e($b['name'])?></a></li>
    <?php endforeach?>
  </ol>
<?php include "footer.php"?>
