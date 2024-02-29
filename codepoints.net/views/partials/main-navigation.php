<?php
/**
 * @var ?array $nav
 */
?>
<?php /* fix for mobile positioning problem. See
  https://www.stevefenton.co.uk/blog/2022/12/mobile-position-sticky-issue/ */ ?>
<div style="position: fixed;"></div>
<cp-navigation>
  <a href="<?=q(url(''))?>" rel="start">
    <svg width="64" height="64"><use href="/static/images/icon.svg#icon"/></svg>
    <span class="title"><?=_q('Home')?><span class="visually-hidden">: <?=q('go to the homepage')?></span></span>
  </a>
<?php
if (! isset($nav) || ! $nav) {
  $nav = [];
} ?>
<?= array_key_exists('prev', $nav) ? $nav['prev'] : '<a href="/search">
  <svg width="64" height="64"><use href="'.static_url('src/images/icons.svg').'#magnifying-glass"/></svg>
  <span class="title">'.__('Search').'</span></a>' ?>
<?= array_key_exists('up', $nav) ? $nav['up'] : '<a href="/planes">
  <svg width="64" height="64" viewBox="194 97 1960 1960"><use href="'.static_url('images/unicode-logo-framed.svg').'#unicode" width="64" height="64"/></svg>
  <span class="title">'.__('All Planes').'</span></a>' ?>
<?= array_key_exists('next', $nav) ? $nav['next'] : '<a href="/random">
  <svg width="64" height="64"><use href="'.static_url('src/images/icons.svg').'#shuffle"/></svg>
  <span class="title">'.__('Random').'</span></a>' ?>
</cp-navigation>
