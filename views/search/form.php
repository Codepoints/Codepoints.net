<form method="get" action="<?php e($router->getUrl('SearchResult'))?>"
      class="extended searchform">
  <p class="stringsearch">
    <label for="s_q">Free search:</label>
    <input type="text" name="q" id="s_q" value="<?php echo _get('q')?>" />
    <small class="nt">Any information about the character, that doesn’t fit the categories below</small>
  </p>
  <p class="stringsearch">
    <label for="s_na">Name:</label>
    <input type="text" name="na" id="s_na" value="<?php echo _get('na')?>" />
    <small class="nt">The Unicode name (or parts) of the character</small>
  </p>
  <p class="stringsearch">
    <label for="s_int">Decimal:</label>
    <input type="number" name="int" id="s_int" value="<?php echo _get('int')?>" />
    <small class="nt">The decimal position of the codepoint</small>
  </p>
  <?php
  foreach (array('blk', 'gc', 'bc', 'ccc', 'dt', 'nt', 'lb', 'ea',
                 'sc', 'SB', 'WB') as $cat) {
    include 'fieldset.php';
  }
  foreach ($info->getBooleanCategories() as $cat):
    $tmp_v = "";
    if (isset($query)) {
        foreach ($query as $q) {
            if ($q[0] === $cat) {
                $tmp_v = $q[2];
                break;
            }
        }
    }
    ?>
  <p class="boolsearch">
    <select name="<?php e($cat)?>" id="s_<?php e($cat)?>" size="3">
      <option value="" <?php if ($tmp_v === ""):?>selected="selected"<?php endif?>>any</option>
      <option value="1" <?php if ($tmp_v == "1"):?>selected="selected"<?php endif?>>yes</option>
      <option value="0" <?php if ($tmp_v == "0"):?>selected="selected"<?php endif?>>no</option>
    </select>
    <label for="s_<?php e($cat)?>"><?php e($info->getCategory($cat))?></label>
  </p>
  <?php endforeach?>
  <p class="submitset"><button type="submit"><span>search</span></button></p>
</form>
