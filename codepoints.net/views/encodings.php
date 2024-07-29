<?php
/**
 * @var list<Array{"slug": string, "label": string}> $encodings
 */

include 'partials/header.php'; ?>
<main class="main main--encodings">

  <h1><?=_q('Browse Codepoints by Encoding')?></h1>
  <section class="bk">
    <ul class="tiles">
      <?php foreach ($encodings as $enc): ?>
        <li>
          <a href="<?=url('encoding/'. $enc['slug'])?>">
            <cp-icon icon="keyboard" width="4em"></cp-icon>
            <?=q($enc['label'])?>
          </a>
        </li>
      <?php endforeach?>
    </ul>
  </section>

</main>
<?php include 'partials/footer.php'; ?>
