<?php if (! isset($searchprefix)) { $searchprefix = 'qs_'; }?>
<form method="get" action="<?php e($router->getUrl('SearchResult'))?>" class="searchform">
  <p><label for="<?php e($searchprefix)?>q">Name:</label>
  <input type="text" name="q" id="<?php e($searchprefix)?>q" value="<?php echo _get('q')?>" /></p>
  <p><button type="submit"><span>search</span></button></p>
</form>
