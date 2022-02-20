<?php
use Codepoints\Unicode\Codepoint;

/**
 * @var Codepoint $codepoint
 * @var ?Codepoint $prev
 * @var ?Codepoint $next
 * @var ?\Codepoints\Unicode\Plane $plane
 * @var ?\Codepoints\Unicode\Block $block
 * @var list<list<Codepoint>> $confusables
 * @var Array $aliases
 * @var string $extra
 * @var ?Array $wikipedia
 * @var Array $othersites
 * @var Array $relatives
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

include 'partials/header.php'; ?>
<main class="main main--codepoint">
  <?php include 'partials/sub-navigation.php' ?>
  <figure class="sqfig cpfig">
    <?=cpimg($codepoint, 250)?>
    <?php if ($codepoint->imagesource): ?>
      <figcaption><?=q(sprintf(__('Source: %s'), $codepoint->imagesource))?></figcaption>
    <?php endif ?>
  </figure>

  <h1><?=str_replace('*', ' <span tabindex="0" aria-description="'.q('This codepoint has no canonical name. The shown name is an alias defined by Unicode.').'" class="no-na tooltip">*</span>', q($title))?></h1>

  <section class="cpdesc cpdesc--desc">
<?php if ($codepoint->gc === 'Xx'): ?>
  <p><?=_q('This codepoint doesn’t exist.')?>
  <?=sprintf(__('If it would, it’d be located in the Nirvana of Undefined Behaviour beyond the 17<sup>th</sup> plane, a land %sno member of the Unicode mailing list has ever seen%s.'),
    '<a href="http://www.unicode.org/mail-arch/unicode-ml/y2003-m10/0234.html">', '</a>')?>
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

<?php if (count($relatives) + count($confusables)):?>
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
      <?php foreach ($codepoint->properties as $key => $value): ?>
        <?php
          if (
            $key === 'cp' ||
            /* empty Unihan properties: skip, b/c unnecessary for most cps */
            (substr($key, 0, 1) === 'k' && ! $value)) { continue; } ?>
        <tr>
          <th scope="row"><?=q(array_get($info->properties, $key, $key))?> <small>(<?=q($key)?>)</small></th>
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
            }, $value);
          else:
            echo '<a rel="nofollow" href="';
            echo q(url('search?'.$key.'='.rawurlencode($value)));
            echo '">';
            echo q($value);
            echo '</a>';
          endif?>
          </td>
        </tr>
      <?php endforeach?>
    </tbody>
  </table>
</section>
</main>
<?php include 'partials/footer.php'; ?>
