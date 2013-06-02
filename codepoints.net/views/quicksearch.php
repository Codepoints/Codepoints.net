<?php if (! isset($searchprefix)) { $searchprefix = 'qs_'; }?>
<form method="get" action="<?php e($router->getUrl('SearchResult'))?>" class="searchform">
<p><label for="<?php e($searchprefix)?>q"><?php _e('Name:')?></label>
  <input type="text" name="q" id="<?php e($searchprefix)?>q" value="<?php echo _get('q')?>" /></p>
  <p><button type="submit"><span><?php _e('search')?></span></button></p>
</form>
