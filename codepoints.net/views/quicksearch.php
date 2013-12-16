<?php if (! isset($searchprefix)) { $searchprefix = 'qs_'; }?>
<form method="get" action="<?php e($router->getUrl('SearchResult'))?>" class="searchform">
  <p><input type="text" name="q" id="<?php e($searchprefix)?>q" value="<?php echo _get('q')?>">
  <button type="submit"><span><?php _e('search')?></span></button></p>
</form>
