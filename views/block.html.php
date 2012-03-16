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
    <?php
    $limits = $pagination->getLimits();
    $block_limits = $block->getLimits();
    $cps = $block->get();
    for ($i = $limits[0]; $i <= $limits[1]; $i++) {
        if ($i + $block_limits[0] > $block_limits[1]) {
            break;
        }
        if (array_key_exists($i + $block_limits[0], $cps)) {
            echo '<li value="' . ($i + $block_limits[0]) . '">'; cp($cps[$i + $block_limits[0]]); echo '</li>';
        } else {
            echo '<li class="missing" value="'.($i + $block_limits[0]).'"><span>'.sprintf('%04X', $i + $block_limits[0]).'</span></li>';
        }
    } ?>
  </ol>
  <?php echo $pagination?>
</div>
<?php include "footer.php"?>
