<?php if (! isset($nav)) {
    $nav = array();
} ?>
<header class="hd">
  <nav>
    <ul class="primary">
      <li class="start"><a href="<?php e($router->getUrl())?>" rel="start">Start</a></li>
      <li class="search"><a href="<?php e($router->getUrl('SearchResult'))?>" rel="search">Search</a></li>
      <?php $stem = array(
          'http://'.$_SERVER['HTTP_HOST'].$router->getUrl('search?'),
          'http://'.$_SERVER['HTTP_HOST'].$router->getUrl('wizard?'),
      );
      if (array_key_exists('HTTP_REFERER', $_SERVER)):
          foreach ($stem as $stemX):
              if (substr($_SERVER['HTTP_REFERER'], 0, strlen($stemX)) === $stemX):?>
                  <li class="up"><a href="<?php e($_SERVER['HTTP_REFERER'])?>">Back to search results</a></li>
      <?php break; endif; endforeach; endif?>
      <li class="scripts"><a href="<?php e($router->getUrl('scripts'))?>">Scripts</a></li>
      <li class="about"><a href="<?php e($router->getUrl().'about')?>">About</a></li>
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
