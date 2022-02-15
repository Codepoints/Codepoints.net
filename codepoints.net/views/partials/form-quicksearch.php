<form method="get" action="<?=q(url('search'))?>" class="searchform searchform--min">
  <p>
    <label><?=_q('Search codepoints.net:')?>
      <input type="text" name="q" value="<?=q(filter_input(INPUT_GET, 'q')?: '')?>">
    </label>
    <button type="submit"><?=_q('search')?></button>
  </p>
</form>
