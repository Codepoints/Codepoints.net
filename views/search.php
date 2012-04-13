<?php
$title = 'Search';
$hDescription = 'Search Codepoints.net.';
include "header.php";
include "nav.php";
?>
<div class="payload search">
  <h1><?php e($title)?></h1>
  <p>Please specify your search parameters.</p>
  <?php include "search/form.php"?>
</div>
<?php include "footer.php"?>
