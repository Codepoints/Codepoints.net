<?php
/**
 * @var \Codepoints\Unicode\PropertyInfo $info
 * @var array<array> $query
 */
?>
<cp-searchform>
<form method="get" action="<?=q(url('search'))?>"
      class="searchform searchform--ext">
  <cp-search-freeform>
    <label for="s_q"><?=_q('Free search:')?></label>
    <input type="text" name="q" id="s_q" value="<?=q($query['q'][0] ?? '')?>">
    <small class="nt"><?=_q('Any information about the character, that doesn’t fit the categories below')?></small>
  </cp-search-freeform>
  <cp-search-freeform>
    <label for="s_na"><?=__('Name:')?></label>
    <input type="text" name="na" id="s_na" value="<?=q($query['na'][0] ?? '')?>">
    <small class="nt"><?=_q('The Unicode name (or parts) of the character')?></small>
  </cp-search-freeform>
  <?php
  foreach ([ 'age', 'blk', 'gc', 'bc', 'ccc', 'dt', 'nt', 'lb', 'ea', 'sc', 'SB', 'WB' ] as $cat) {
    include 'form-fieldset.php';
  }
  foreach ($info->booleans as $cat):
    $tmp_v = '';
    if (array_key_exists($cat, $query)) {
        $tmp_v = $query[$cat][0];
    }
?>
  <cp-search-boolean>
    <select name="<?=q($cat)?>" size="3">
      <option value=""<?php if ($tmp_v === ''):?> selected<?php endif?>><?=__('any')?></option>
      <option value="1"<?php if ($tmp_v === '1'):?> selected<?php endif?>><?=__('yes')?></option>
      <option value="0"<?php if ($tmp_v === '0'):?> selected<?php endif?>><?=__('no')?></option>
    </select>
    <label slot="desc"><?= q(array_get($info->properties, $cat, $cat))?></label>
  </cp-search-boolean>
  <?php endforeach?>
  <p class="submitset">
    <button type="submit"><?=_q('search')?></button>
    <button type="reset" onclick="return confirm('<?=_q('Really remove all selected values?')?>')"><?=_q('reset all fields')?></button>
    <button id="wizard" type="button" onclick="document.querySelector('cp-wizard').hidden = false">🧙 <?=_q('try the wizard')?></button>
  </p>
  <cp-wizard hidden></cp-wizard>
</form>
</cp-searchform>
