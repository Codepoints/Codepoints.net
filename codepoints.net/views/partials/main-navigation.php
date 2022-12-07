<?php
/**
 * @var ?array $nav
 */
?>
<cp-navigation>
  <a href="<?=q(url(''))?>" rel="start" class="ln">
    <svg width="16" height="16"><use href="/static/images/icon.svg#icon"/></svg>
    <span class="title"><?=_q('Home')?></span>
  </a>
<?php
if (! isset($nav) || ! $nav) {
  $nav = [];
} ?>
<?= array_key_exists('prev', $nav) ? $nav['prev'] : '<a href="/search">
  <svg width="16" height="16"><use href="/static/images/magnifying-glass.svg#root"/></svg>
  <span class="title">'._('Search').'</span></a>' ?>
<?= array_key_exists('up', $nav) ? $nav['up'] : '<a href="/planes">
  <svg width="16" height="16"><svg viewBox="194 97 1960 1960" width="100%" height="100%"><use href="'.static_url('images/unicode-logo-framed.svg#unicode').'" width="16" height="16"/></svg></svg>
  <span class="title">'._('All Planes').'</span></a>' ?>
<?= array_key_exists('next', $nav) ? $nav['next'] : '<a href="/random">
  <svg width="16" height="16"><use href="'.static_url('images/shuffle.svg#root').'"/></svg>
  <span class="title">'._('Random').'</span></a>' ?>
</cp-navigation>
