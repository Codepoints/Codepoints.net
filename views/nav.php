<?php if (! isset($nav)) {
    $nav = array();
} ?>
<nav>
  <ul>
    <li class="start"><a href="<?php e($router->getUrl())?>" rel="start">Unicode</a></li>
    <?php foreach($nav as $rel => $link):?>
      <li class="<?php e($rel)?>"><?php echo $link?></li>
    <?php endforeach?>
    <li class="search"><a href="<?php e($router->getUrl('SearchResult'))?>" rel="search">Search</a></li>
    <li class="about"><a href="<?php e($router->getUrl().'about')?>">About</a></li>
  </ul>
</nav>
