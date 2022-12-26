<?php
/**
 * @var \Codepoints\Unicode\Codepoint $codepoint
 * @var \Codepoints\Unicode\PropertyInfo $info
 */

$repr = $codepoint->representation; ?>
<cp-representations cp="<?=q((string)$codepoint->id)?>">
<table class="props representations">
  <thead>
    <tr>
      <th scope="col"><?=_q('System')?></th>
      <th scope="col"><?=_q('Representation')?></th>
    </tr>
  </thead>
  <tbody>
    <tr class="primary" data-system="nr">
      <th scope="row"><?=_q('Nº')?></th>
      <td class="repr-number"><?=$codepoint->id?></td>
    </tr>
    <tr class="primary" data-system="utf-8">
      <th scope="row"><?=_q('UTF-8')?></th>
      <td><?=q($repr('UTF-8'))?></td>
    </tr>
    <tr class="primary" data-system="utf-16">
      <th scope="row"><?=_q('UTF-16')?></th>
      <td><?=q($repr('UTF-16'))?></td>
    </tr>
    <tr data-system="utf-32">
      <th scope="row"><?=_q('UTF-32')?></th>
      <td><?=q($repr('UTF-32'))?></td>
    </tr>
    <tr data-system="url">
      <th scope="row"><?=_q('URL-Quoted')?></th>
      <td><?=q($repr('URL'))?></td>
    <tr data-system="html">
      <th scope="row"><?=_q('HTML-Escape')?></th>
      <td><?=q($repr('HTML'))?></td>
    </tr>
    <?php
    ini_set('mbstring.substitute_character', 'none');
    $moji = mb_convert_encoding($codepoint->chr(), 'UTF-8', 'ISO-8859-1');
    if ($moji && $moji !== $codepoint->chr()): ?>
      <tr data-system="mojibake">
        <th scope="row" title="<?=_q('approx. ISO-8859-1, Latin 1, “us-ascii”, ...')?>"><?=_q('Wrong windows-1252 Mojibake')?></th>
        <td><?php echo $moji; ?></td>
      </tr>
    <?php endif ?>
<?php $aliases = $codepoint->aliases;
$typeMap = [
    'abbreviation' => __('abbreviation'),
    'alias' => __('alias'),
    'alternate' => __('alternate'),
    'control' => __('control'),
    'correction' => __('correction'),
    'digraph' => __('digraph'),
    'figment' => __('figment'),
    'html' => __('HTML-Escape'),
    'latex' => '<span class="latex">L<sup>A</sup>T<sub>E</sub>X</span>',
];
    foreach ($aliases as $alias):?>
      <tr data-system="<?=q($alias['type'])?>">
        <th scope="row"><?php if (array_key_exists($alias['type'], $typeMap)) {
            echo $typeMap[$alias['type']];
        } elseif (substr($alias['type'], 0, 4) === 'enc:') {
            echo q(sprintf(__('Encoding: %s (hex bytes)'),
                strtoupper(substr($alias['type'], 4))));
        } else {
            echo q($alias['type']);
        }?></th>
        <td><?=q($alias['alias'])?></td>
      </tr>
    <?php endforeach?>
    <?php $pronunciation = $codepoint->pronunciation;
    if ($pronunciation):?>
      <tr data-system="pinyin">
        <th scope="row">Pīnyīn</th>
        <td><?=q($pronunciation)?></td>
      </tr>
    <?php endif?>
    <?php foreach (['kIRG_GSource', 'kIRG_HSource', 'kIRG_JSource',
    'kIRG_KPSource', 'kIRG_KSource', 'kIRG_MSource', 'kIRG_TSource',
    'kIRG_USource', 'kIRG_VSource', 'kBigFive', 'kCCCII', 'kCNS1986',
    'kCNS1992', 'kEACC', 'kGB0', 'kGB1', 'kGB3', 'kGB5', 'kGB7', 'kGB8',
    'kHKSCS', 'kIBMJapan', 'kJis0', 'kJIS0213', 'kJis1', 'kKPS0', 'kKPS1',
    'kKSC0', 'kKSC1', 'kMainlandTelegraph', 'kPseudoGB1',
    'kTaiwanTelegraph', 'kXerox'] as $v):
      if (isset($props[$v]) && $props[$v]):?>
        <tr data-system="<?=q($v)?>">
          <th scope="row"><?=q(array_get($info->properties, $v, $v))?></th>
          <td><?=q($props[$v])?></td>
        </tr>
    <?php endif; endforeach?>
  </tbody>
</table>
</cp-representations>
