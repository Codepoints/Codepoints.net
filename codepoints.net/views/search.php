<?php
$title = __('Search');
$hDescription = __('Search Codepoints.net by specifying many different possible parameters.');
$canonical = '/search';
include "header.php";
include "nav.php";
?>
<div class="payload search">
  <h1><?php e($title)?></h1>
  <p><?php printf(__('Please add search limits with the form below. Click “add new query”, select a category and choose one of the values. You can change the value afterwards, if you click on it again. The %s button on the right removes the value from the search again.'), '<span style="width:1.4em" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only"><span class="ui-button-icon-primary ui-icon ui-icon-close"></span><span class="ui-button-text" style="line-height: 0.4em">'.__('remove').'</span></span>')?></p>
  <?php include "search/form.php"?>
</div>
<?php
$footer_scripts = array(url('/static/js/searchform.js'));
include "footer.php"?>
