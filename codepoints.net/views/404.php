<?php
/**
 * @var list<\Codepoints\Unicode\Codepoint> $cps
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
</main>
<?php include 'partials/footer.php'; ?>
