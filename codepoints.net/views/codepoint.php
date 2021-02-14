<?php include 'partials/header.php'; ?>
<main class="main main--codepoint">
  <figure>
    <?=cpimg($codepoint, 250)?>
  </figure>
  <h1><?=q($codepoint)?> <?=q($codepoint->name)?></h1>
Block: <?=bl($block)?><br>
Plane: <?=pl($plane)?><br>
Prev: <?=cp($prev)?><br>
Next: <?=cp($next)?><br>
</main>
<?php include 'partials/footer.php'; ?>
