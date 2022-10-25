<?php
/**
 * @var \Codepoints\Unicode\PropertyInfo $info
 */
?>
<?php
function _get(string $name) : string {
    return q($_GET[$name] ?? '');
}
?>
<cp-searchform>
<form method="get" action="<?=q(url('search'))?>"
      class="searchform searchform--ext">
  <p class="search__widget search__widget--string">
  <label for="s_q"><?=__('Free search:')?></label>
    <input type="text" name="q" id="s_q" value="<?php echo _get('q')?>">
    <small class="nt"><?=__('Any information about the character, that doesn’t fit the categories below')?></small>
  </p>
  <p class="search__widget search__widget--string">
  <label for="s_na"><?=__('Name:')?></label>
    <input type="text" name="na" id="s_na" value="<?php echo _get('na')?>">
    <small class="nt"><?=__('The Unicode name (or parts) of the character')?></small>
  </p>
  <p class="search__widget search__widget--string">
    <label for="s_int"><?=__('Decimal:')?></label>
    <input type="number" name="int" id="s_int" value="<?php echo _get('int')?>">
    <small class="nt"><?=__('The decimal position of the codepoint')?></small>
  </p>
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
  <p class="submitset"><button type="submit"><span><?=__('search')?></span></button></p>
</form>
</cp-searchform>
