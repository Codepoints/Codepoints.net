<?php
/**
 * @var list<\Codepoints\Unicode\Codepoint> $cps
 * @var string $page_description
 * @var string $q
 */

include 'partials/header.php'; ?>
<main class="main main--404">
  <h1><?=q($title)?></h1>
  <p><?=q((string)$page_description)?></p>
  <form>
    <p>
      <label>
        <?=_q('Your text:')?>
        <input type="text" name="q" value="<?=q($q)?>" maxlength="256">
      </label>
      <button type="submit"><?=_q('analyze')?></button>
    </p>
  </form>
  </form>
  <?php if (count($cps)):?>
    <ul class="tiles">
      <?php foreach($cps as $cp):?>
      <li><?=cp($cp)?></li>
      <?php endforeach?>
    </ul>
  <?php endif?>
</main>
<?php include 'partials/footer.php'; ?>
