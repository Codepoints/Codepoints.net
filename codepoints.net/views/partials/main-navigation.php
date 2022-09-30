<cp-navigation>
  <a slot="home" href="<?=q(url(''))?>" rel="start" class="ln">
    <svg width="16" height="16"><use href="/static/images/icon.svg#icon"/></svg>
    <span class="meta"><?=_q('Home')?></span>
  </a>
<?php
/** @psalm-suppress RedundantCondition */
if (isset($nav) && count($nav)):
    if (array_key_exists('prev', $nav)) { echo str_replace('<a ', '<a slot="prev" ', $nav['prev']); }
    if (array_key_exists('up', $nav)) { echo str_replace('<a ', '<a slot="up" ', $nav['up']); }
    if (array_key_exists('next', $nav)) { echo str_replace('<a ', '<a slot="next" ', $nav['next']); }
endif?>
</cp-navigation>
