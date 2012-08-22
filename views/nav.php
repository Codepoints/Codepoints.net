<?php if (! isset($nav)) {
    $nav = array();
} ?>
<header class="hd">
  <nav>
    <ul class="primary">
      <li class="start"><a href="<?php e($router->getUrl())?>" rel="start"><?php _e('Start')?></a></li>
      <li class="search"><a href="<?php e($router->getUrl('SearchResult'))?>" rel="search"><?php _e('Search')?></a></li>
      <?php $stem = array(
          'http://'.$_SERVER['HTTP_HOST'].$router->getUrl('search?'),
          'http://'.$_SERVER['HTTP_HOST'].$router->getUrl('wizard?'),
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
