<?php
$nav = [];
if ($prev) {
  $nav['prev'] = cp($prev);
}
if ($block) {
  $nav['up'] = bl($block);
}
if ($next) {
  $nav['next'] = cp($next);
}

include 'partials/header.php'; ?>
<main class="main main--codepoint">
  <figure>
    <?=cpimg($codepoint, 250)?>
  </figure>
  <h1><?=q($title)?></h1>

<?php if ($codepoint->gc === 'Xx'): ?>
  <p><?=_q('This codepoint doesn’t exist.')?>
  If it would, it’d be located in the
  Nirvana of Undefined Behaviour beyond the 17<sup>th</sup> plane, a land <a href="http://www.unicode.org/mail-arch/unicode-ml/y2003-m10/0234.html">no member of the Unicode mailing list has ever seen</a>.
  </p>
<?php endif ?>

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

<table class="props">
  <thead>
    <tr>
      <th><?=_q('Property')?></th>
      <th><?=_q('Value')?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($codepoint->properties as $k => $v): ?>
      <tr>
        <th><?=q(array_get($info->get('properties'), $k, $k))?> <small>(<?=q($k)?>)</small></th>
        <td>
        <?php if ($v === '' || $v === null):?>
          <span class="x">—</span>
        <?php elseif (in_array($k, $info->get('booleans'))):?>
          <span class="<?=($v)?'y':'n'?>"><?=($v)?'✔':'✘'?></span>
        <?php elseif ($v instanceof \Codepoints\Unicode\Codepoint):?>
          <?=cp($v)?>
        <?php elseif (is_array($v)):?>
          <?php foreach ($v as $_v): ?>
            <?php if ($_v instanceof \Codepoints\Unicode\Codepoint): ?>
              <?=cp($_v)?>
            <?php elseif ($k === 'scx'): ?>
              <a href="<?=q(url('search?sc='.$_v))?>"><?=array_get($info->get('script'), $_v, $_v)?></a>
            <?php else: ?>
              <?=q($_v)?>
            <?php endif ?>
          <?php endforeach ?>
        <?php elseif (in_array($k, ['kCompatibilityVariant', 'kDefinition',
            'kSemanticVariant', 'kSimplifiedVariant',
            'kSpecializedSemanticVariant', 'kTraditionalVariant', 'kZVariant'])):
          echo preg_replace_callback('/U\+([0-9A-F]{4,6})/', function(Array $m) use ($codepoint) : string {
            if (hexdec($m[1]) === $codepoint->id) {
                return cp($codepoint);
            }
            return $m[0]; #cp(Codepoint::getCP(hexdec($m[1]), $db), '', 'min');
          }, $v);
        else:
          echo q($v);
        endif?>
        </td>
      </tr>
    <?php endforeach?>
  </tbody>
</table>
</main>
<?php include 'partials/footer.php'; ?>
