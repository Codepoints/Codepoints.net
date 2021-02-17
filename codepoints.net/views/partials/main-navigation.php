<nav class="main-nav">
  <ul class="main-nav__primary">
    <li class="start"><a href="<?=q(url(''))?>" rel="start"><?=_q('Start')?></a></li>
    <li class="search"><a href="<?=q(url('search'))?>" rel="search"><?=_q('Search')?></a></li>
    <li class="scripts"><a href="<?=q(url('scripts'))?>"><?=_q('Scripts')?></a></li>
    <li class="random"><a rel="nofollow" href="<?=q(url('random'))?>"><?=_q('Random')?></a></li>
    <li class="about"><a rel="nofollow" href="<?=q(url('about'))?>"><?=_q('About')?></a></li>
  </ul>
  <?php

  /** @psalm-suppress RedundantCondition */
  if (isset($nav) && count($nav)):?>
    <ul class="secondary">
      <?php foreach($nav as $rel => $link):?>
        <li class="<?=q($rel)?>"><?=$link?></li>
      <?php endforeach?>
    </ul>
  <?php endif?>
</nav>
