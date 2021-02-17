<?php include 'partials/header.php'; ?>
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
  <?=$extra?>
<?php endif ?>

<?php if ($abstract): ?>
  <p><?php printf(__('The %sWikipedia%s has the following information about this codepoint:'), '<a href="'.q($abstract['src']).'">', '</a>')?></p>
  <blockquote>
    <?php echo strip_tags($abstract['abstract'], '<p><b><strong class="selflink"><strong><em><i><var><sup><sub><tt><ul><ol><li><samp><small><hr><h2><h3><h4><h5><dfn><dl><dd><dt><u><abbr><big><blockquote><br><center><del><ins><kbd>')?>
  </blockquote>
<?php endif ?>

<?php if ($block): ?>
  Block: <?=bl($block)?><br>
<?php endif ?>
<?php if ($plane): ?>
  Plane: <?=pl($plane)?><br>
<?php else: ?>
<?php endif ?>
<?php if ($prev): ?>
  Prev: <?=cp($prev)?><br>
<?php endif ?>
<?php if ($next): ?>
  Next: <?=cp($next)?><br>
<?php endif ?>
<table class="props">
  <thead>
    <tr>
      <th><?=_q('Property')?></th>
      <th><?=_q('Value')?></th>
    </tr>
  </thead>
  <tbody>
<?php

    $bools = ['Bidi_M', 'Bidi_C', 'CE', 'Comp_Ex', 'XO_NFC',
    'XO_NFD', 'XO_NFKC', 'XO_NFKD', 'Join_C', 'Upper', 'Lower', 'OUpper',
    'OLower', 'CI', 'Cased', 'CWCF', 'CWCM', 'CWL', 'CWKCF', 'CWT', 'CWU',
    'IDS', 'OIDS', 'XIDS', 'IDC', 'OIDC', 'XIDC', 'Pat_Syn', 'Pat_WS', 'Dash',
    'Hyphen', 'QMark', 'Term', 'STerm', 'Dia', 'Ext', 'SD', 'Alpha', 'OAlpha',
    'Math', 'OMath', 'Hex', 'AHex', 'DI', 'ODI', 'LOE', 'WSpace', 'Gr_Base',
    'Gr_Ext', 'OGr_Ext', 'Gr_Link', 'Ideo', 'UIdeo', 'IDSB', 'IDST',
    'Radical', 'Dep', 'VS', 'NChar'];
foreach ($codepoint->getInfo('properties') as $k => $v):
        if (! in_array($k, ['cp', 'image', 'abstract']) && ! ($k[0] === 'k' && ! $v)):?>
      <tr>
        <th><?=q($k)?> <small>(<?=q($k)?>)</small></th>
        <td>
        <?php if ($v === '' || $v === null):?>
          <span class="x">—</span>
        <?php elseif (in_array($k, $bools)):?>
          <span class="<?=($v)?'y':'n'?>"><?=($v)?'✔':'✘'?></span>
        <?php elseif ($v instanceof \Codepoints\Unicode\Codepoint):?>
          <?=cp($v)?>
        <?php elseif (is_array($v)):?>
            <?php foreach ($v as $_cp): ?>
                <?=cp($_cp)?>
            <?php endforeach ?>
        <?php elseif ($k === 'scx'):
        foreach(explode(' ', $v) as $sc):?>
            <a rel="nofollow" href="<?=q(url('search?sc='.$v))?>"><?=q($sc)?></a>
        <?php endforeach;
        elseif (in_array($k, ['kCompatibilityVariant', 'kDefinition',
            'kSemanticVariant', 'kSimplifiedVariant',
            'kSpecializedSemanticVariant', 'kTraditionalVariant', 'kZVariant'])):
          echo preg_replace_callback('/U\+([0-9A-F]{4,6})/', function(Array $m) use ($codepoint) : string {
            if (hexdec($m[1]) === $codepoint->id) {
                return cp($codepoint);
            }
            return 'TODO'; #cp(Codepoint::getCP(hexdec($m[1]), $db), '', 'min');
          }, $v);
        else:
          echo q($v);
        endif?>
        </td>
      </tr>
    <?php endif; endforeach?>
  </tbody>
</table>
</main>
<?php include 'partials/footer.php'; ?>
