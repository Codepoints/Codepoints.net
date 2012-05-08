<?php
$query = $result->getQuery();
$cQuery = count($query);
$cBlocks = count($blocks);
$cBResult = $cBlocks > 0? ($cBlocks > 1?
    sprintf(' and %s Blocks', $cBlocks) :
    ' and 1 Block') :
    '';
$fQuery = $result->getCount();
$title = $fQuery > 0? ($fQuery > 1?
    sprintf('%s Codepoints%s Found', $fQuery, $cBResult) :
    sprintf('1 Codepoint%s Found', $cBResult)) :
    sprintf('No Codepoints%s Found', $cBResult);
$hDescription = 'These codepoints match the given search.';
include "header.php";
include "nav.php";
?>
<div class="payload search">
  <h1><?php e($title)?></h1>
  <?php if ($fQuery > 0):?>
    <p><strong><?php e($fQuery)?></strong> codepoints match<?php include "result/querytext.php"?></p>
    <?php echo $pagination?>
    <ol class="block data">
      <?php foreach ($result->get() as $cp => $na):
        echo '<li value="' . $cp . '">'; cp($na); echo '</li>';
      endforeach ?>
    </ol>
    <?php echo $pagination?>
  <?php else:?>
    <p>There are no matches<?php include "result/querytext.php"?></p>
  <?php endif?>
  <?php if($cBlocks):?>
  <p><strong><?php e($cBlocks)?></strong> block<?php if($cBlocks > 1):?>s<?php endif?>
    match<?php if($cBlocks === 1):?>es<?php endif?>
    <strong><?php e(_get('q'))?></strong>:<p>
    <ol class="data">
      <?php foreach ($blocks as $bl):
        echo '<li>'; bl($bl); echo '</li>';
      endforeach ?>
    </ol>
  <?php endif?>
  <?php if (isset($wizard) && $wizard):?>
    <p>
      <a href="<?php e($router->getUrl('wizard'))?>">Try “Find My Codepoint” again.</a>
    </p>
  <?php endif?>
  <?php include "search/form.php"?>
</div>
<?php include "footer.php"?>
