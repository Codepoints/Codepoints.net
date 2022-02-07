<?php
/**
 * @var ?\Codepoints\Unicode\SearchResult $search_result
 * @var ?Array $alt_result
 * @var ?\Codepoints\Router\Pagination $pagination
 * @var bool $wizard
 * @var string $q
 */

include 'partials/header.php' ?>
<main class="main main--search"<?php if ($search_result):?> data-count="<?=q((string)$search_result->count())?>"<?php endif ?>>
  <h1><?=_q($title)?></h1>
  <p><?php printf(__('Please add search limits with the form below. Click “add new query”, select a category and choose one of the values. You can change the value afterwards, if you click on it again. The %s button on the right removes the value from the search again.'), __('remove'))?></p>

  <?php if ($search_result && $search_result->count()): ?>
    <ol class="tiles">
      <?php foreach ($search_result as $codepoint): ?>
        <?php if (! $codepoint) { continue; } ?>
        <li><?=cp($codepoint)?></li>
      <?php endforeach ?>
    </ol>
    <?=$pagination?>
  <?php elseif ($alt_result && count($alt_result)): ?>
    <p><?=_q('The following codepoints match:')?></p>
    <ol class="tiles">
      <?php foreach ($alt_result as $codepoint): ?>
        <?php if (! $codepoint) { continue; } ?>
        <li><?=cp($codepoint)?></li>
      <?php endforeach ?>
    </ol>
  <?php endif ?>

  <?php if (isset($blocks) && $blocks): ?>
    <p><?php printf(count($blocks) === 1? __('%s block matches %s:') : __('%s blocks match %s:'), '<strong>'.count($blocks).'</strong>', '<strong>'.q($q).'</strong>')?><p>
    <ol class="tiles">
      <?php foreach ($blocks as $block): ?>
        <li><?=bl($block)?></li>
      <?php endforeach ?>
    </ol>
  <?php endif ?>

<?php if ($wizard): ?>
  <?php if ($search_result): ?>
    <p><a href="?"><?=_q('Try “Find My Codepoint” again.')?></a></p>
  <?php else: ?>
    <p><?=_q('You search for a specific character? Answer the following questions and we try to figure out candidates.')?></p>
  <?php endif ?>
<?php else: ?>
  <form method="get">
    <input type="search" name="q" value="<?=q($q)?>" required><br>
    <button>search</button>
  </form>
<?php endif ?>

</main>
<?php include 'partials/footer.php' ?>
