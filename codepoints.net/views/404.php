<?php
/**
 * @var list<\Codepoints\Unicode\Codepoint> $cps
 * @var string $match
 */

include 'partials/header.php'; ?>
<main class="main main--404">
  <h1><?=q($title)?></h1>
  <?php if (count($cps)):?>
    <ul class="tiles">
      <?php foreach($cps as $cp):?>
      <li><?=cp($cp)?></li>
      <?php endforeach?>
    </ul>
  <?php endif?>
  <p><?=_q('Search other codepoints:')?></p>
  <?php $quicksearch_value = $match; include 'partials/form-quicksearch.php'; ?>
</main>
<?php include 'partials/footer.php'; ?>
