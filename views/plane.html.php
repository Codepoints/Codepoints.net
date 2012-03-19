<?php
$title = $plane->getName();
$blocks = $plane->getBlocks();
$prev = $plane->getPrev();
$next = $plane->getNext();
include "header.php";
?>
<div class="payload plane">
  <nav>
    <ul>
      <?php if ($prev):?>
        <li class="prev"><a rel="prev" href="<?php e($router->getUrl($prev))?>"><?php e($prev->name)?></a></li>
      <?php endif?>
      <li class="up"><a rel="up" href="<?php e($router->getUrl())?>">Unicode</a></li>
      <?php if ($next):?>
        <li class="next"><a rel="next" href="<?php e($router->getUrl($next))?>"><?php e($next->name)?></a></li>
      <?php endif?>
    </ul>
  </nav>
  <h1><?php e($title);?></h1>
  <p>From U+<?php f('%04X', $plane->first)?>
     to U+<?php f('%04X', $plane->last)?></p>
  <h2>Blocks in this plane</h2>
  <ol>
    <?php foreach ($blocks as $b):?>
      <li><a href="<?php e($router->getUrl($b))?>"><?php e($b->getName())?></a></li>
    <?php endforeach?>
  </ol>
</div>
<?php include "footer.php"?>
