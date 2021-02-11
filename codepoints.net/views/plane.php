<div class="payload plane">
  <h1><?=q($title)?></h1>
  <p><?php printf(__('Plane from U+%04X to U+%04X.'), $plane->first, $plane->last)?></p>
  <?php if (count($plane->blocks)):?>
    <h2><?=_q('Blocks in this plane')?></h2>
    <ol>
      <?php foreach ($plane->blocks as $block):?>
        <li><?=q($block)?> <small><?php printf(__('(U+%04X to U+%04X)'), $block->first, $block->last)?></small></li>
      <?php endforeach?>
    </ol>
  <?php else:?>
    <p class="info"><?_q('There are no blocks defined in this plane.')?></p>
  <?php endif?>
</div>
