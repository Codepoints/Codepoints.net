<?php
/**
 * @var ?\Codepoints\Unicode\SearchResult $search_result
 * @var ?\Codepoints\Router\Pagination $pagination
 * @var string $q
 * @var Array $env
 * @var ?boolean $is_range
 */

/* prevent indexing of search pages. We do not want the crawlers
 accessing this page due to the extra resources these renderings cost. */
$noindex = isset($is_range)? '' : '<meta name="robots" content="noindex">';

/* add some info needed by JS */
$script_age = json_encode($env['info']->script_age);
$region_to_block = json_encode($env['info']->region_to_block);

$head_extra = <<<HEAD_EXTRA
    $noindex
    <script>
    var script_age = $script_age;
    var region_to_block = $region_to_block;
    </script>
HEAD_EXTRA;
include 'partials/header.php' ?>
<main class="main main--search"<?php if ($search_result):?> data-count="<?=q((string)$search_result->count())?>"<?php endif ?>>
  <h1><?=_q($title)?></h1>

  <?php if ($search_result && $search_result->count()): ?>
    <?php if ($search_result->count() > 16): ?>
        <p><a href="#searchform"><?=_q('Jump to the search form')?></a></p>
    <?php endif ?>
    <ol class="tiles">
      <?php foreach ($search_result as $codepoint): ?>
        <?php if (! $codepoint) { continue; } ?>
        <li><?=cp($codepoint)?></li>
      <?php endforeach ?>
    </ol>
    <?=$pagination?>
  <?php endif ?>

  <?php if (isset($blocks) && $blocks): ?>
    <p><?php printf(count($blocks) === 1? __('%s block matches %s:') : __('%s blocks match %s:'), '<strong>'.count($blocks).'</strong>', '<strong>'.q($q).'</strong>')?><p>
    <ol class="tiles">
      <?php foreach ($blocks as $block): ?>
        <li><?=bl($block)?></li>
      <?php endforeach ?>
    </ol>
  <?php endif ?>

  <p id="searchform">
    <?=_q('Search for code points:')?>
  </p>
  <?php include 'partials/form-fullsearch.php' ?>
  <ol>
    <li>
      <?=_q('Choose properties of code points to search for.')?>
      <?=_q('The easiest is the “free search” field where you can place any information that you have.')?>
      <?=_q('We will then try our best to match code points with it.')?>
    </li>
    <li><?=_q('If you know a part of the actual Unicode name enter it in the “name” field.')?></li>
    <li>
      <?=_q('Click a button with a ≡ icon to restrict the search to certain properties only.')?>
      <?=_q('A dialog opens with possible options.')?>
    </li>
    <li>
      <?=_q('Click a button with a * icon to enforce a specific yes/no property.')?>
      <?=_q('Click again to search for code points <em>without</em> this property and a third time to reset the search again.')?>
    </li>
    <li><?=_q('Click “search” to start the search.')?></li>
  </ol>
  <p>
    <?=_q('On code point detail pages you can click the values in the property description to be guided to a search page that shows code points with the same property.')?>
  </p>

  <?php if ($q && isset($search_result)): ?>
  <script type="application/tracker+json">
    <?= str_replace('</', '&lt;/', json_encode(['trackSiteSearch', $q, false,
    (isset($is_fulltext_result) && $is_fulltext_result)?
      $search_result->count() :
      0 /* make sure that we know in the analytics view, that this search term
        * wasn't found in the fulltext table. */
    ]))?>
  </script>
  <?php endif ?>
</main>
<?php include 'partials/footer.php' ?>
