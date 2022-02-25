<?php
/**
 * @var \Codepoints\Unicode\Plane $plane
 * @var ?\Codepoints\Unicode\Plane $prev
 * @var ?\Codepoints\Unicode\Plane $next
 */

$nav = [];
if ($prev) {
  $nav['prev'] = pl($prev, 'prev');
}
$nav['up'] = sprintf('<a class="pl" rel="up" href="%s"><img src="/static/images/unicode-logo-framed.svg" alt="" width="16" height="16"> %s</a>', url('planes'), __('Unicode'));
if ($next) {
  $nav['next'] = pl($next, 'next');
}

include 'partials/header.php'; ?>
<main class="main main--plane">
  <?php include 'partials/sub-navigation.php' ?>
  <figure class="sqfig plfig">
    <?=plimg($plane, 250)?>
    <figcaption><?=_q('Source: Font Last Resort')?></figcaption>
  </figure>
  <h1><?=q($title)?></h1>
  <p><?php printf(__('Plane from U+%04X to U+%04X.'), $plane->first, $plane->last)?></p>
  <p><a href="/planes"><?=_q('all planes')?></a></p>
<?php if ($prev): ?>
  <p>Prev: <?=pl($prev)?></p>
<?php endif ?>
<?php if ($next): ?>
  <p>Next: <?=pl($next)?></p>
<?php endif ?>
  <?php if (count($plane->blocks)):?>
    <h2><?=_q('Blocks in this plane')?></h2>
    <ol class="tiles">
      <?php foreach ($plane->blocks as $block):?>
        <li><?=bl($block)?></li>
      <?php endforeach?>
    </ol>
  <?php else:?>
    <p class="info"><?_q('There are no blocks defined in this plane.')?></p>
  <?php endif?>
</main>
<?php include 'partials/footer.php'; ?>
