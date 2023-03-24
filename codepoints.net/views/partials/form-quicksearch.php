<?php
/**
 * @var string $quicksearch_value
 */
?>
<form method="get" action="<?=q(url('search'))?>" class="searchform searchform--min" role="search">
  <p>
    <label><?=_q('Search codepoints.net:')?>
      <input type="text" name="q" value="<?=q($quicksearch_value)?>" inputmode="search">
    </label>
    <button type="submit"><?=_q('search')?></button>
  </p>
</form>
