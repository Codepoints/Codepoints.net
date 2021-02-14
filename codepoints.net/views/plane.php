<?php include 'partials/header.php'; ?>
<div class="payload plane">
  <figure>
    <?=plimg($plane, 128)?>
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
    <ol>
      <?php foreach ($plane->blocks as $block):?>
        <li><?=bl($block)?></li>
      <?php endforeach?>
    </ol>
  <?php else:?>
    <p class="info"><?_q('There are no blocks defined in this plane.')?></p>
  <?php endif?>
</div>
<?php include 'partials/footer.php'; ?>
