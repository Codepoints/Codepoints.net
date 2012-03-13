<?php $title = 'Visual Unicode';
include "header.php"?>
  <h1><?php e($title)?></h1>
  <ol>
    <?php foreach ($planes as $plane):?>
      <li><a href="<?php e($router->getUrl($plane))?>"><?php e($plane->name)?></a></li>
    <?php endforeach?>
  </ol>
<?php include "footer.php"?>
