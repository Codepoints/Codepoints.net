<?php $title = 'Codepoints';
$hDescription = 'Codepoints is a site dedicated to the Unicode standard.';
include "header.php";
include "nav.php";
?>
<div class="payload front">
  <h1><?php e($title)?></h1>
  <p>The <a href="<?php e($router->getUrl('planes'))?>">Unicode Planes</a>
  </p>
  <ol>
    <?php foreach ($planes as $plane):?>
      <li><a href="<?php e($router->getUrl($plane))?>"><?php e($plane->name)?></a></li>
    <?php endforeach?>
  </ol>
</div>
<?php include "footer.php"?>
