<?php
/** @psalm-suppress RedundantCondition */
if (isset($nav) && count($nav)):?>
  <nav class="sub-nav">
    <ul class="sub-nav__list">
      <li class="sub-nav__listitem prev"><?php
        if (array_key_exists('prev', $nav)) { echo $nav['prev']; }
      ?></li>
      <li class="sub-nav__listitem up"><?php
        if (array_key_exists('up', $nav)) { echo $nav['up']; }
      ?></li>
      <li class="sub-nav__listitem next"><?php
        if (array_key_exists('next', $nav)) { echo $nav['next']; }
      ?></li>
    </ul>
  </nav>
<?php endif?>
