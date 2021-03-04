<form method="get" class="langchooser">
  <label>
    <span class="visually-hidden"><?=_q('Language:')?></span>
    <select name="lang" onchange="this.form.submit()">
      <option value="en"<?php if ($lang === 'en'):?> selected<?php endif?>>english</option>
      <option value="de"<?php if ($lang === 'de'):?> selected<?php endif?>>deutsch</option>
      <option value="es"<?php if ($lang === 'es'):?> selected<?php endif?>>español</option>
      <option value="pl"<?php if ($lang === 'pl'):?> selected<?php endif?>>polski</option>
    </select>
  </label>
  <?php foreach ($_GET as $k => $v): if ($k !== 'lang'):?>
    <?php if (is_array($v)): ?>
      <?php foreach ($v as $vv):?>
        <input type="hidden" name="<?=q($k)?>[]" value="<?=q($vv)?>">
      <?php endforeach?>
    <?php else: ?>
      <input type="hidden" name="<?=q($k)?>" value="<?=q($v)?>">
    <?php endif ?>
  <?php endif;endforeach?>
  <noscript><button type="submit"><?=_q('choose language')?></button></noscript>
</form>
