<?php
$title = 'Search';
$hDescription = 'Search Codepoints.net.';
include "header.php";
include "nav.php";
?>
<div class="payload search">
  <h1><?php e($title)?></h1>
  <p>Please add search limits with the form below. Click “add new query”,
  select a category and choose one of the values. You can change the value
  afterwards, if you click on it again. The
  <span style="width:1.4em" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only"><span class="ui-button-icon-primary ui-icon ui-icon-close"></span><span class="ui-button-text" style="line-height: 0.4em">remove</span></span>
  button on the right removes the value from the search again.</p>
  <?php include "search/form.php"?>
</div>
<?php
$footer_scripts = array('/static/js/searchform.js');
include "footer.php"?>
