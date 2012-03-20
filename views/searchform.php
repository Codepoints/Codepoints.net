<form method="get" action="<?php $router->getUrl('SearchResult')?>"
      class="extended searchform">
  <p><label for="s_na">Name:</label>
     <input type="text" name="na" id="s_na" value="" /></p>
  <p><label for="s_int">Decimal:</label>
     <input type="text" name="int" id="s_int" value="<?php echo _get('int')?>" /></p>
  <fieldset><legend><?php e($info->getCategory('blk'))?>:</legend>
    <?php foreach($info->getLegendForCategory('blk') as $k => $v):?>
      <p>
        <input type="checkbox" name="blk[]" value="<?php e($k)?>"
               id="s_blk_<?php e($k)?>" />
        <label for="s_blk_<?php e($k)?>"><?php e($v)?></label>
      </p>
    <?php endforeach?>
  </fieldset>
  <fieldset><legend><?php e($info->getCategory('gc'))?>:</legend>
    <?php foreach($info->getLegendForCategory('gc') as $k => $v):?>
      <p>
        <input type="checkbox" name="gc[]" value="<?php e($k)?>"
               id="s_gc_<?php e($k)?>" />
        <label for="s_gc_<?php e($k)?>"><?php e($v)?></label>
      </p>
    <?php endforeach?>
  </fieldset>
  <fieldset><legend><?php e($info->getCategory('bc'))?>:</legend>
    <?php foreach($info->getLegendForCategory('bc') as $k => $v):?>
      <p>
        <input type="checkbox" name="bc[]" value="<?php e($k)?>"
               id="s_bc_<?php e($k)?>" />
        <label for="s_bc_<?php e($k)?>"><?php e($v)?></label>
      </p>
    <?php endforeach?>
  </fieldset>
  <fieldset><legend><?php e($info->getCategory('ccc'))?>:</legend>
    <?php foreach($info->getLegendForCategory('ccc') as $k => $v):?>
      <p>
        <input type="checkbox" name="ccc[]" value="<?php e($k)?>"
               id="s_ccc_<?php e($k)?>" />
        <label for="s_ccc_<?php e($k)?>"><?php e($v[1])?></label>
      </p>
    <?php endforeach?>
  </fieldset>
  <fieldset><legend><?php e($info->getCategory('dt'))?>:</legend>
    <?php foreach($info->getLegendForCategory('dt') as $k => $v):?>
      <p>
        <input type="checkbox" name="dt[]" value="<?php e($k)?>"
               id="s_dt_<?php e($k)?>" />
        <label for="s_dt_<?php e($k)?>"><?php e($v)?></label>
      </p>
    <?php endforeach?>
  </fieldset>
  <fieldset><legend><?php e($info->getCategory('nt'))?>:</legend>
    <?php foreach($info->getLegendForCategory('nt') as $k => $v):?>
      <p>
        <input type="checkbox" name="nt[]" value="<?php e($k)?>"
               id="s_nt_<?php e($k)?>" />
        <label for="s_nt_<?php e($k)?>"><?php e($v)?></label>
      </p>
    <?php endforeach?>
  </fieldset>
  <fieldset><legend><?php e($info->getCategory('lb'))?>:</legend>
    <?php foreach($info->getLegendForCategory('lb') as $k => $v):?>
      <p>
        <input type="checkbox" name="lb[]" value="<?php e($k)?>"
               id="s_lb_<?php e($k)?>" />
        <label for="s_lb_<?php e($k)?>"><?php e($v)?></label>
      </p>
    <?php endforeach?>
  </fieldset>
  <fieldset><legend><?php e($info->getCategory('ea'))?>:</legend>
    <?php foreach($info->getLegendForCategory('ea') as $k => $v):?>
      <p>
        <input type="checkbox" name="ea[]" value="<?php e($k)?>"
               id="s_ea_<?php e($k)?>" />
        <label for="s_ea_<?php e($k)?>"><?php e($v)?></label>
      </p>
    <?php endforeach?>
  </fieldset>
  <fieldset><legend><?php e($info->getCategory('sc'))?>:</legend>
    <?php foreach($info->getLegendForCategory('sc') as $k => $v):?>
      <p>
        <input type="checkbox" name="sc[]" value="<?php e($k)?>"
               id="s_sc_<?php e($k)?>" />
        <label for="s_sc_<?php e($k)?>"><?php e($v)?></label>
      </p>
    <?php endforeach?>
  </fieldset>
  <fieldset><legend><?php e($info->getCategory('SB'))?>:</legend>
    <?php foreach($info->getLegendForCategory('SB') as $k => $v):?>
      <p>
        <input type="checkbox" name="SB[]" value="<?php e($k)?>"
               id="s_SB_<?php e($k)?>" />
        <label for="s_SB_<?php e($k)?>"><?php e($v)?></label>
      </p>
    <?php endforeach?>
  </fieldset>
  <fieldset><legend><?php e($info->getCategory('WB'))?>:</legend>
    <?php foreach($info->getLegendForCategory('WB') as $k => $v):?>
      <p>
        <input type="checkbox" name="WB[]" value="<?php e($k)?>"
               id="s_WB_<?php e($k)?>" />
        <label for="s_WB_<?php e($k)?>"><?php e($v)?></label>
      </p>
    <?php endforeach?>
  </fieldset>
  <?php foreach ($info->getBooleanCategories() as $cat):?>
  <p>
    <select name="<?php e($cat)?>" id="s_<?php e($cat)?>" size="3">
        <option value="">any</option>
        <option value="1">yes</option>
        <option value="0">no</option>
    </select>
    <label for="s_<?php e($cat)?>"><?php e($info->getCategory($cat))?></label>
  </p>
  <?php endforeach?>
  <p><button type="submit"><span>search</span></button></p>
</form>
