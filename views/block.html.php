<?php
$title = 'Block ' . $block->getName();
$block_limits = $block->getBoundaries();
$prev = $block->getPrev();
$next = $block->getNext();
$plane = $block->getPlane();
$pagination = new Pagination(count($block->get()));
$page = isset($_GET['page'])? intval($_GET['page']) : 1;
$pagination->setPage($page);
include "header.php";
$nav = array();
if ($prev) {
    $nav['prev'] = _bl($prev, 'prev', 'min', 'span');
}
$nav["up"] = '<a class="pl" rel="up" href="'.q($router->getUrl($plane)).'">'.q($plane->getName()).'</a>';
if ($next) {
    $nav['next'] = _bl($next, 'next', 'min', 'span');
}
include "nav.php";
?>
<div class="payload block">
  <figure>
    <img src="static/images/blocks/<?php e(str_replace(' ', '_', $block->getName()))?>.png" alt="" width="128" height="128" />
  </figure>
  <h1><?php e($block->getName());?></h1>
  <?php if ($block_limits === Null):?>
    <p class="warning">This block has not defined any codepoints.</p>
  <?php else:?>
    <p>Block from U+<?php f('%04X', $block_limits[0])?>
       to U+<?php f('%04X', $block_limits[1])?></p>
    <p><a href="http://www.unicode.org/charts/PDF/U<?php f('%04X', $block_limits[0])?>.pdf">Chart at Unicode.org</a> (PDF)</p>
    <?php echo $pagination?>
      <ol class="block data">
        <?php
        $limits = $pagination->getLimits();
        $cps = $block->get();
        for ($i = $limits[0]; $i < $limits[1]; $i++) {
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
  <?php endif?>
</div>
<?php include "footer.php"?>
