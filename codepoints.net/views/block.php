<?php include 'partials/header.php'; ?>
<div class="payload block">
  <figure>
    <?=blimg($block, 128)?>
  </figure>
  <h1><?=q($title);?></h1>
  <p>
     <?php printf(__('Block from U+%04X to U+%04X.'), $block->first, $block->last)?>
     <?php printf(__('This block was introduced in Unicode version %s (%s). It contains %s codepoints.'),
       ''/*$block->getVersion()*/,
       ''/*$info->getYearForAge($block->getVersion())*/,
      '<strong>'.$block->count().'</strong>')?></p>
<?php if ($prev): ?>
  <p>Prev: <?=bl($prev)?></p>
<?php endif ?>
<?php if ($next): ?>
  <p>Next: <?=bl($next)?></p>
<?php endif ?>
  <p>Plane: <?=pl($block->plane)?></p>
  <?php if (iterator_count($block) === 0):?>
    <p><?php printf(__('This block has not defined any codepoints between U+%04X and U+%04X.'), $block_limits[0], $block_limits[1])?></p>
  <?php else:?>
    <p><a href="http://www.unicode.org/charts/PDF/U<?php printf('%04X', $block->first)?>.pdf"><?=_q('Chart at Unicode.org')?></a> <?=_q('(PDF)')?><br>
    <a href="http://decodeunicode.org/<?=q(str_replace(' ', '_', strtolower($block->name)))?>"><?=_q('Block at Decode Unicode')?></a></p>
    <ol>
    <?php foreach ($block as $codepoint): ?>
      <li><?=cp($codepoint, 16)?></li>
      <?php endforeach ?>
    </ol>
  <?php endif?>
</div>
<?php include 'partials/footer.php'; ?>
