<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Codepoint not found</title>
    <?php if ($siblings[0]):?>
      <link href="U+<?php echo $siblings[0]?>" rel="prev">
    <?php endif?>
    <?php if ($siblings[1]):?>
      <link href="U+<?php echo $siblings[1]?>" rel="next">
    <?php endif?>
  </head>
  <body>
    <h1>Codepoint not Found</h1>
    <dl>
      <?php if ($siblings[0]):?>
        <dt>Previous</dt>
        <dd><a href="U+<?php echo $siblings[0]?>">U+<?php echo $siblings[0]?></a></dd>
      <?php endif?>
      <?php if ($siblings[1]):?>
        <dt>Next</dt>
        <dd><a href="U+<?php echo $siblings[1]?>">U+<?php echo $siblings[1]?></a></dd>
      <?php endif?>
    </dl>
  </body>
</html>
