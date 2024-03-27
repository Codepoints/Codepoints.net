<?php
use Codepoints\Unicode\Codepoint;

/**
 * @var Codepoint $codepoint
 * @var ?Codepoint $prev
 * @var ?Codepoint $next
 * @var ?\Codepoints\Unicode\Plane $plane
 * @var ?\Codepoints\Unicode\Block $block
 * @var \Codepoints\Unicode\PropertyInfo $info
 * @var ?Array{ name: ?string } $csur
 * @var ?list<list<Codepoint>> $confusables
 * @var \Codepoints\Unicode\PropertyInfo $info
 * @var Array $aliases
 * @var string $title
 * @var ?string $page_description
 * @var string $extra
 * @var string $lang
 * @var ?Array $wikipedia
 * @var Array $othersites
 * @var ?Array $relatives
 * @var \Codepoints\Database $db
 */

$nav = [];
if ($prev) {
  $nav['prev'] = cp($prev, 'prev');
}
if ($block) {
  $nav['up'] = bl($block, 'up');
}
if ($next) {
  $nav['next'] = cp($next, 'next');
}

$head_extra = (new \Codepoints\View('partials/head-codepoint'))(compact('codepoint', 'block', 'plane', 'prev', 'next', 'title', 'page_description', 'lang'));
include 'partials/header.php'; ?>
<main class="main main--codepoint" data-cp="<?=q((string)$codepoint->id)?>" data-sensitivity="<?=q((string)$codepoint->sensitivity->value)?>">
  <?php /* we need the wrapper div to have the figure not float to the absolute
         * left but remain close to the text */ ?>
  <div>
    <figure class="sqfig cpfig">
      <cp-copy content="<?= q(mb_chr($codepoint->id)) ?>"><?=cpimg($codepoint, 250)?></cp-copy>
      <?php if ($codepoint->imagesource): ?>
        <figcaption><?=q(sprintf(__('Source: %s'), $codepoint->imagesource))?></figcaption>
      <?php endif ?>
    </figure>
  </div>

  <h1><?=sprintf('<span class="title__cp">%s</span> <span class="title__na">%s</span>', (string)$codepoint, format_codepoint_name($codepoint->name))?></h1>

  <aside>
    <div class="cp-toolbox cp-toolbox--profile">
      <dl>
        <dt><?=_q('Nº')?></dt>
        <dd><?=q((string)$codepoint->id)?></dd>
        <?php foreach(['gc', 'sc', 'bc', 'dt'] as $cat):
          if (! $codepoint->properties) { continue; }?>
          <dt><?=q($info->properties[$cat])?></dt>
          <dd><a rel="nofollow" href="<?=q('search?'.$cat.'='.$codepoint->properties[$cat])?>"><?=q($info->getLegend($cat, $codepoint->properties[$cat]))?></a></dd>
        <?php endforeach?>
        <?php if ($codepoint->properties && $codepoint->properties['nt'] !== 'None'):?>
          <dt><?=_q('Numeric Value')?></dt>
          <dd><a rel="nofollow" href="<?=q('search?nt='.$codepoint->properties['nt'])?>"><?=q($info->getLegend('nt', $codepoint->properties['nt']).' '.$codepoint->properties['nv'])?></a></dd>
        <?php endif?>
      </dl>
    </div>
    <div class="cp-toolbox cp-toolbox--tools">
      <cp-copy content="<?= q(mb_chr($codepoint->id)) ?>"><button type="button"><?=_q('copy to clipboard')?></button></cp-copy>
      <cp-btn-share><a class="btn" href="mailto:?subject=<?=q(rawurlencode((string)$codepoint .' '. format_codepoint_name($codepoint->name)))?>&amp;body=<?=q(rawurlencode(url($codepoint, true)))?>"><cp-icon icon="share" width="16" height="16"></cp-icon> <?=_q('share this codepoint')?></a></cp-btn-share>
      <cp-btn-embed><button type="button"><?=_q('embed this codepoint')?></button></cp-btn-embed>
    </div>
  </aside>

  <section class="cpdesc cpdesc--desc">
<?php if ($codepoint->gc === 'Xx'): ?>
  <p><?=_q('This codepoint doesn’t exist.')?>
  <?=sprintf(__('If it would, it’d be located in the Nirvana of Undefined Behaviour beyond the 17<sup>th</sup> plane, a land %sno member of the Unicode mailing list has ever seen%s.'),
    '<a href="https://www.unicode.org/mail-arch/unicode-ml/y2003-m10/0234.html">', '</a>')?>
  </p>
<?php elseif ($codepoint->gc === 'Cn' && $codepoint->name === '<reserved>'): ?>
  <p><?=_q('This codepoint doesn’t exist.')?>
  <?php
    if ($plane):
      printf(__('If it would, it’d be located in the %s.'), pl($plane, 'up'));
    endif;
    if ($block):
        printf(' '.q(__('You can find surrounding codepoints in the block %s.')), bl($block, ''));
    endif?>
  </p>
  <p>
    <?php printf(q(__('The Unicode Consortium adds new codepoints to the standard all the time. Visit %stheir website%s to find out about pending codepoints and whether this one is in the pipe.')), '<a href="https://www.unicode.org/alloc/Pipeline.html">', '</a>')?>
    <?=_q('The following table shows typical representations of how the codepoint would look, if it existed. This may help you when debugging, but is not of real use otherwise.')?>
  </p>
<?php else: ?>
    <?=$codepoint->description?>
<?php endif ?>
  </section>

<?php if ($extra): ?>
  <section class="cpdesc cpdesc--extra">
    <?=$extra?>
  </section>
<?php endif ?>

<?php if (isset($csur) && $csur['name']): ?>
  <section class="cpdesc cpdesc--csur">
    <p><?=sprintf(_q('The %sUnder-ConScript Unicode Registry%s contains this private-use character with the name %s.'), '<a href="https://www.kreativekorp.com/ucsur/">', '</a>', $csur['name'])?></p>
  </section>
<?php endif ?>

<?php if ($wikipedia): ?>
  <section class="cpdesc cpdesc--wikipedia">
    <p><?php printf(__('The %sWikipedia%s has the following information about this codepoint:'), '<a href="'.q($wikipedia['src']).'">', '</a>')?></p>
    <blockquote>
      <?php echo strip_tags($wikipedia['abstract'], '<p><b><strong class="selflink"><strong><em><i><var><sup><sub><tt><ul><ol><li><samp><small><hr><h2><h3><h4><h5><dfn><dl><dd><dt><u><abbr><big><blockquote><br><center><del><ins><kbd>')?>
    </blockquote>
  </section>
<?php endif ?>

  <section class="cpdesc cpdesc--repr">
    <h2><?=_q('Representations')?></h2>
    <?php include 'partials/codepoint-representations.php' ?>
  </section>

<?php if ($relatives!== null && $confusables !== null && count($relatives) + count($confusables)):?>
  <section class="cpdesc cpdesc--relatives">
    <h2><?=_q('Related Characters')?></h2>
    <?php if (count($relatives)):?>
      <ul class="tiles">
        <?php foreach ($relatives as $rel):?>
          <li><?=cp($rel)?></li>
        <?php endforeach?>
      </ul>
    <?php endif?>
    <?php if (count($confusables)):?>
      <h3 id="confusables"><?=_q('Confusables')?></h3>
      <div class="container--confusables">
        <?php foreach ($confusables as $set): $first = true; ?>
      <ul class="tiles tiles--confusables">
          <?php foreach ($set as $rel): ?>
            <li<?php echo $first? ' class="first"' : ''?>><?=cp($rel)?></li>
          <?php $first = false; endforeach?>
      </ul>
        <?php endforeach?>
      </div>
    <?php endif?>
  </section>
<?php endif?>

<?php if ($othersites): ?>
  <section class="cpdesc cpdesc--othersites">
    <h2><?=_q('Elsewhere')?></h2>
    <ul>
      <?php foreach ($othersites as $label => $url): ?>
        <li><a href="<?=q($url)?>"><?=q($label)?></a></li>
      <?php endforeach ?>
    </ul>
  </section>
<?php endif ?>

<section class="cpdesc cpdesc--record">
  <h2><?=_q('Complete Record')?></h2>
  <table class="props">
    <thead>
      <tr>
        <th scope="col"><?=_q('Property')?></th>
        <th scope="col"><?=_q('Value')?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ((array)$codepoint->properties as $key => $value): ?>
        <?php
          if (
            $key === 'cp' ||
            /* empty Unihan properties: skip, b/c unnecessary for most cps */
            (substr($key, 0, 1) === 'k' && ! $value)) { continue; } ?>
        <tr>
          <th scope="row"><cp-glossary-term term="<?=q($key)?>"><?=q(array_get($info->properties, $key, $key))?> <small>(<?=q($key)?>)</cp-glossary-term></small></th>
          <td>
          <?php if ($value === '' || $value === null):?>
            <span class="x">—</span>
          <?php elseif (in_array($key, $info->booleans)):?>
            <span class="<?=($value)?'y':'n'?>"><?=($value)?'✔':'✘'?></span>
          <?php elseif ($value instanceof \Codepoints\Unicode\Codepoint):?>
            <?=cp($value)?>
          <?php elseif (is_array($value)):?>
            <?php foreach ($value as $_v): ?>
              <?php if ($_v instanceof \Codepoints\Unicode\Codepoint): ?>
                <?=cp($_v)?>
              <?php elseif ($key === 'scx'): ?>
                <a href="<?=q(url('search?sc='.$_v))?>"><?=array_get($info->script, $_v, $_v)?></a>
              <?php else: ?>
                <?=q($_v)?>
              <?php endif ?>
            <?php endforeach ?>
          <?php elseif (in_array($key, ['kCompatibilityVariant', 'kDefinition',
              'kSemanticVariant', 'kSimplifiedVariant',
              'kSpecializedSemanticVariant', 'kTraditionalVariant', 'kZVariant'])):
            echo preg_replace_callback('/U\+([0-9A-F]{4,6})/', function(Array $m) use ($codepoint, $db) : string {
              if (hexdec($m[1]) === $codepoint->id) {
                  return cp($codepoint);
              }
              return cp(Codepoint::getCached(['cp' => hexdec($m[1]), 'name' => $m[0], 'gc' => 'Lo'], $db));
            }, (string)$value);
          else:
            echo '<a rel="nofollow" href="';
            echo q(url('search?'.$key.'='.rawurlencode($value)));
            echo '">';
            if ($key === 'nv' && $value === 'NaN') {
                echo __('not a number');
            } else {
                echo q($info->getLegend($key, $value));
            }
            echo '</a>';
          endif?>
          </td>
        </tr>
      <?php endforeach?>
    </tbody>
  </table>
</section>
<?php if (array_key_exists('embed', $_GET)): ?>
  <p class="embed-info"><a href="<?=q(url($codepoint, true))?>" target="_blank"><?=_q('» View this character on Codepoints.net')?></a></p>
<?php endif ?>
</main>
<?php include 'partials/footer.php'; ?>
