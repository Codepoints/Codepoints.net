<?php
$title = 'Page not Found';
if ($block) {
    $title = 'Codepoint not Found';
}
include "header.php";
include "nav.php";
?>
<div class="payload error">
  <h1><?php e($title)?></h1>
  <?php if ($block):?>
    <p>This codepoint doesn’t exist. You can find surrounding codepoints in
      the block <?php bl($block, '', 'min')?>.</p>
  <?php endif?>
  <?php $searchprefix = 'err_'; include "quicksearch.php"; unset($searchprefix); ?>
</div>
<?php include "footer.php"?>
