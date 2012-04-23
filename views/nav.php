<?php if (! isset($nav)) {
    $nav = array();
} ?>
<nav>
  <ul class="primary">
    <li class="start"><a href="<?php e($router->getUrl())?>" rel="start">Start</a></li>
    <li class="search"><a href="<?php e($router->getUrl('SearchResult'))?>" rel="search">Search</a></li>
    <?php $stem = 'http://'.$_SERVER['HTTP_HOST'].$router->getUrl('search?');
    if (substr($_SERVER['HTTP_REFERER'], 0, strlen($stem)) === $stem):?>
      <li class="up"><a href="<?php e($_SERVER['HTTP_REFERER'])?>">Back to last search</a></li>
    <?php endif?>
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
