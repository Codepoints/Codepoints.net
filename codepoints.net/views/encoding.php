<?php
/**
 * @var string $label
 * @var list<Array{count: int, block: \Codepoints\Unicode\Block}> $blocks
 * @var \Codepoints\Unicode\SearchResult $result
 * @var \Codepoints\Router\Pagination $pagination
 * @var Array $aliases
 */

include 'partials/header.php'; ?>
<main class="main main--encoding">

  <h1><?=sprintf(__('Encoding %s'), $label)?></h1>
  <p><?=sprintf(__('Browse the %s code points covered by this character set.'), $result->count())?></p>
  <ul class="tiles table">
    <?php foreach ($result as $codepoint):
        if (! $codepoint) { continue; }
      ?>
      <li>
        <?=cp($codepoint, '', '', $aliases[$codepoint->id] ?? null)?>
      </li>
    <?php endforeach?>
  </ul>
  <?=$pagination?>
  <h2><?=_q('Blocks')?></h2>
  <p><?=_q('The characters on this page are contributed by the following Unicode Blocks:')?></p>
  <ul>
  <?php
        foreach ($blocks as $block) {
            printf('<li><strong style="display:inline-block;min-width:2em;text-align:right">%d</strong> code points from %s</li>', $block['count'], bl($block['block']));
        }
  ?>
  </ul>
  <p><a href="<?=url('encoding')?>"><?=_q('Back to the list of encodings')?></a></p>

</main>
<?php include 'partials/footer.php'; ?>
