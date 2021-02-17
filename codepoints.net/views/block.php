<?php include 'partials/header.php'; ?>
<main class="main main--block">
  <figure>
    <?=blimg($block, 128)?>
  </figure>
  <h1><?=q($title);?></h1>
  <p>
     <?php printf(__('Block from U+%04X to U+%04X.'), $block->first, $block->last)?>
     <?php printf(__('This block was introduced in Unicode version %s (%s). It contains %s codepoints.'),
       $age,
       ''/*$info->getYearForAge($block->getVersion())*/,
      '<strong>'.$block->count().'</strong>')?></p>

<?php if ($abstract): ?>
  <p><?php printf(__('The %sWikipedia%s provides the following information on block %s:'), '<a href="'.$abstract['src'].'">', '</a>', $block->name)?></p>
  <blockquote cite="<?=q($abstract['src'])?>">
    <?=strip_tags($abstract['abstract'],
    '<p><b><strong class="selflink"><strong><em><i><var><sup><sub><tt><ul><ol><li><samp><small><hr><h2><h3><h4><h5><dfn><dl><dd><dt><u><abbr><big><blockquote><br><center><del><ins><kbd>')?>
  </blockquote>
<?php endif ?>
<?php if ($prev): ?>
  <p>Prev: <?=bl($prev)?></p>
<?php endif ?>
<?php if ($next): ?>
  <p>Next: <?=bl($next)?></p>
<?php endif ?>
  <p>Plane: <?=pl($block->plane)?></p>
  <?php if (! $block->count()):?>
    <p><?php printf(__('This block has not defined any codepoints between U+%04X and U+%04X.'), $block->first, $block->last)?></p>
  <?php else:?>
    <p><a href="http://www.unicode.org/charts/PDF/U<?php printf('%04X', $block->first)?>.pdf"><?=_q('Chart at Unicode.org')?></a> <?=_q('(PDF)')?><br>
    <a href="https://decodeunicode.org/en/blocks/<?=q(str_replace(' ', '_', strtolower($block->name)))?>"><?=_q('Block at Decode Unicode')?></a></p>
    <ol>
      <?php foreach ($pagination->slice() as $cp => $codepoint): ?>
        <li>
          <?php if ($codepoint): ?>
            <?=cp($codepoint)?>
          <?php else: ?>
            <span class="missing-cp">U+<?=sprintf('%04X', $cp)?></span>
          <?php endif ?>
        </li>
      <?php endforeach ?>
    </ol>
    <?=$pagination?>
  <?php endif?>
</main>
<?php include 'partials/footer.php'; ?>
