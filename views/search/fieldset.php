<fieldset class="propsearch">
  <legend><?php e($info->getCategory($cat))?>:</legend>
  <?php foreach($info->getLegendForCategory($cat) as $k => $v):?>
    <p>
      <input type="checkbox" name="<?php e($cat)?>[]" value="<?php e($k)?>"
             id="s_<?php e($cat)?>_<?php e($k)?>"
             <?php if (isset($_GET[$cat]) && in_array($k, $_GET[$cat])):?>checked="checked"<?php endif?>
             />
      <label for="s_<?php e($cat)?>_<?php e($k)?>"><?php if (is_array($v)) {
            e(end($v));
        } else {
            e($v);
        }?></label>
    </p>
  <?php endforeach?>
</fieldset>
