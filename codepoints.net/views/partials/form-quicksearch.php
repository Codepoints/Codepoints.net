<?php if (! isset($searchprefix)) { $searchprefix = 'qs_'; }?>
<form method="get" action="<?=q(url('search'))?>" class="searchform">
  <p><input type="text" name="q" id="<?=q($searchprefix)?>q" value="<?=q(filter_input(INPUT_GET, 'q')?: '')?>">
  <button type="submit"><?=q('search')?></button></p>
</form>
