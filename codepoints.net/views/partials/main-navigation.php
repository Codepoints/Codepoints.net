<nav class="main-nav">
  <ul class="main-nav__primary">
    <li class="start"><a href="<?=q(url(''))?>" rel="start">
      <img src="/static/images/icon.svg" width="16" height="16">
      <?=_q('Codepoints')?></a></li>
    <li class="search"><a href="<?=q(url('search'))?>" rel="search">
      <svg width="16" height="16"><use xlink:href="/api/v1/glyph/1F50E#U1F50E"/></svg>
      <?=_q('Search')?></a></li>
    <li class="scripts"><a href="<?=q(url('scripts'))?>">
      <svg width="16" height="16"><use xlink:href="/api/v1/glyph/1F310#U1F310"/></svg>
      <?=_q('Scripts')?></a></li>
    <li class="random"><a rel="nofollow" href="<?=q(url('random'))?>">
      <svg width="16" height="16"><use xlink:href="/api/v1/glyph/27F3#U27F3"/></svg>
      <?=_q('Random')?></a></li>
    <li class="about"><a rel="nofollow" href="<?=q(url('about'))?>">
      <svg width="16" height="16"><use xlink:href="/api/v1/glyph/1F6C8#U1F6C8"/></svg>
      <?=_q('About')?></a></li>
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
