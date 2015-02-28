<?php if (! isset($nav)) {
    $nav = array();
} ?>
<header class="hd">
  <nav>
    <form method="get" class="langchooser">
      <select name="lang" onchange="this.form.submit()">
        <option value="en"<?php if ($lang === 'en'):?> selected="selected"<?php endif?>>english</option>
        <option value="de"<?php if ($lang === 'de'):?> selected="selected"<?php endif?>>deutsch</option>
      </select>
      <?php foreach ($_GET as $k => $v): if ($k !== 'lang'):?>
        <?php if (is_array($v)): ?>
          <?php foreach ($v as $vv):?>
            <input type="hidden" name="<?php e($k)?>[]" value="<?php e($vv)?>" />
          <?php endforeach?>
        <?php else: ?>
          <input type="hidden" name="<?php e($k)?>" value="<?php e($v)?>" />
        <?php endif ?>
      <?php endif;endforeach?>
      <noscript><button type="submit"><?php _e('choose language')?></button></noscript>
    </form>
    <ul class="primary">
      <li class="start"><a href="<?php e($router->getUrl())?>" rel="start"><?php _e('Start')?></a></li>
      <li class="search"><a href="<?php e($router->getUrl('SearchResult'))?>" rel="search"><?php _e('Search')?></a></li>
      <?php $stem = array(
          'http://'.$_SERVER['HTTP_HOST'].$router->getUrl('search?'),
          'http://'.$_SERVER['HTTP_HOST'].$router->getUrl('wizard?'),
          'https://'.$_SERVER['HTTP_HOST'].$router->getUrl('search?'),
          'https://'.$_SERVER['HTTP_HOST'].$router->getUrl('wizard?'),
      );
      if (array_key_exists('HTTP_REFERER', $_SERVER)):
          foreach ($stem as $stemX):
              if (substr($_SERVER['HTTP_REFERER'], 0, strlen($stemX)) === $stemX):?>
                  <li class="up"><a href="<?php e($_SERVER['HTTP_REFERER'])?>"><?php _e('Back to search results')?></a></li>
      <?php break; endif; endforeach; endif?>
      <li class="scripts"><a href="<?php e($router->getUrl('scripts'))?>"><?php _e('Scripts')?></a></li>
      <li class="random"><a href="<?php e($router->getUrl().'random')?>"><?php _e('Random')?></a></li>
      <li class="about"><a href="<?php e($router->getUrl().'about')?>"><?php _e('About')?></a></li>
    </ul>
    <?php if (count($nav)):?>
      <ul class="secondary">
        <?php foreach($nav as $rel => $link):?>
          <li class="<?php e($rel)?>"><?php echo $link?></li>
        <?php endforeach?>
      </ul>
    <?php endif?>
  </nav>
</header>
