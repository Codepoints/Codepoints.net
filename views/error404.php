<?php
$title = 'Page not Found';
if ($block) {
    $title = 'Codepoint not Found';
}
$hDescription = 'HTTP error 404: This page doesn’t exist.';
include "header.php";
include "nav.php";
?>
<div class="payload error">
  <h1><?php e($title)?></h1>
  <?php if ($block):?>
    <p>This codepoint doesn’t exist. You can find surrounding codepoints in
      the block <?php bl($block, '', 'min')?>.</p>
  <?php endif?>
  <?php if (count($cps)):?>
    <ul class="data">
      <?php foreach($cps as $cp):?>
      <li><?php cp($cp)?></li>
      <?php endforeach?>
    </ul>
  <?php endif?>
  <?php $searchprefix = 'err_'; include "quicksearch.php"; unset($searchprefix); ?>
</div>
<?php include "footer.php"?>
