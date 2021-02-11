<?php include 'partials/header.php'; ?>
<?=q($codepoint)?> <?=q($codepoint->name)?>
<br>
Block: <?=bl($block)?><br>
Plane: <?=pl($plane)?><br>
Prev: <?=cp($prev)?><br>
Next: <?=cp($next)?><br>
<?=cpimg($codepoint, 250)?>
<?php include 'partials/footer.php'; ?>
