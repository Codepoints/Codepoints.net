<fieldset class="propsearch">
  <legend><?php e($info->getCategory($cat))?>:</legend>
  <?php foreach($info->getLegendForCategory($cat) as $k => $v):
    $tmp_v = array();
    if (isset($query)) {
        foreach ($query as $q) {
            if ($q[0] === $cat) {
                $tmp_v = $q[2];
                break;
            }
        }
    }
    ?>
    <p>
      <input type="checkbox" name="<?php e($cat)?>[]" value="<?php e($k)?>"
             id="s_<?php e($cat)?>_<?php e($k)?>"
             <?php if (in_array($k, $tmp_v)):?>checked="checked"<?php endif?>
             />
      <label for="s_<?php e($cat)?>_<?php e($k)?>"><?php if (is_array($v)) {
            e(end($v));
        } else {
            e($v);
        }?></label>
    </p>
  <?php endforeach?>
</fieldset>
