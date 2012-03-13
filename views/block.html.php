<?php
$title = 'Block ' . $block->getName();
$bounds = $block->getBoundaries();
$prev = $block->getPrev();
$next = $block->getNext();
$plane = $block->getPlane();
$pagination = new Pagination(count($block->get()));
$page = isset($_GET['page'])? intval($_GET['page']) : 1;
$pagination->setPage($page);
include "header.php";
?>
<div class="payload block">
  <nav>
    <ul>
      <li class="prev">
        <?php if ($prev):?>
          <a class="bl" rel="prev" href="<?php e($router->getUrl($prev))?>"><?php e($prev->getName())?></a>
        <?php endif?>
      </li>
      <li class="up">
        <a class="pl" rel="up" href="<?php e($router->getUrl($plane))?>"><?php e($plane->getName())?></a>
      </li>
      <li class="next">
        <?php if ($next):?>
          <a class="bl" rel="next" href="<?php e($router->getUrl($next))?>"><?php e($next->getName())?></a>
        <?php endif?>
      </li>
    </ul>
  </nav>
  <figure>
  <img src="static/blocks/<?php e(str_replace(' ', '_', $block->getName()))?>.png" alt=""/>
  </figure>
  <h1><?php e($title);?></h1>
  <p>From U+<?php f('%04X', $bounds[0])?>
     to U+<?php f('%04X', $bounds[1])?></p>
  <?php echo $pagination?>
  <ol class="block-data">
    <?php foreach ($pagination->getSet($block->get()) as $cp => $na):
      echo '<li value="' . $cp . '">'; cp($na); echo '</li>';
    endforeach ?>
  </ol>
  <?php echo $pagination?>
</div>
<?php include "footer.php"?>
