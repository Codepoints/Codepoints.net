<?php include 'partials/header.php' ?>
<main class="main main--search">
  <h1><?=_q($title)?></h1>
  <p><?php printf(__('Please add search limits with the form below. Click “add new query”, select a category and choose one of the values. You can change the value afterwards, if you click on it again. The %s button on the right removes the value from the search again.'), __('remove'))?></p>

  <?php if ($search_result): ?>
    <ol>
      <?php foreach ($search_result as $cp => $codepoint): ?>
        <li><?=cp($codepoint)?></li>
      <?php endforeach ?>
    </ol>
    <?=$pagination?>
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