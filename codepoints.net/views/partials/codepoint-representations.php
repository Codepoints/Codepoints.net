<?php $repr = $codepoint->representation; ?>
<table class="props representations">
  <thead>
    <tr>
      <th><?=_q('System')?></th>
      <th><?=_q('Representation')?></th>
    </tr>
  </thead>
  <tbody>
    <tr class="primary">
      <th><?=_q('Nº')?></th>
      <td class="repr-number"><?=$codepoint->id?></td>
    </tr>
    <tr class="primary">
      <th><?=_q('UTF-8')?></th>
      <td><?=q($repr('UTF-8'))?></td>
    </tr>
    <tr class="primary">
      <th><?=_q('UTF-16')?></th>
      <td><?=q($repr('UTF-16'))?></td>
    </tr>
    <tr>
      <th><?=_q('UTF-32')?></th>
      <td><?=q($repr('UTF-32'))?></td>
    </tr>
    <tr>
      <th><?=_q('URL-Quoted')?></th>
      <td><?=q($repr('URL'))?></td>
    <tr>
      <th><?=_q('HTML-Escape')?></th>
      <td><?=q($repr('HTML'))?></td>
    </tr>
    <?php
    ini_set('mbstring.substitute_character', "none");
    $moji = mb_convert_encoding($codepoint->chr(), 'UTF-8', 'ISO-8859-1');
    if ($moji): ?>
      <tr>
        <th title="<?=_q('approx. ISO-8859-1, Latin 1, “us-ascii”, ...')?>"><?=_q('Wrong windows-1252 Mojibake')?></th>
        <td><?php echo $moji; ?></td>
      </tr>
    <?php endif ?>
<?php $alias = $codepoint->aliases;
$typeMap = [
    'abbreviation' => __('abbreviation'),
    'alias' => __('alias'),
    'alternate' => __('alternate'),
    'control' => __('control'),
    'correction' => __('correction'),
    'digraph' => __('digraph'),
    'figment' => __('figment'),
    'html' => __('HTML-Escape'),
    'latex' => '<span class="latex">L<sup>a</sup>T<sub>e</sub>X</span>',
];
    foreach ($alias as $a):?>
      <tr<?php if (in_array($a['type'], ['html', 'abbreviation', 'alias'])):?>
         class="primary"
        <?php endif?>>
        <th><?php if (array_key_exists($a['type'], $typeMap)) {
            echo $typeMap[$a['type']];
        } elseif (substr($a['type'], 0, 4) === 'enc:') {
            echo q(sprintf(__('Encoding: %s (hex bytes)'),
                strtoupper(substr($a['type'], 4))));
        } else {
            echo q($a['type']);
        }?></th>
        <td><?php if ($a['type'] === 'html') {
            echo '&amp;';
        }
        echo q($a['alias']);
        if ($a['type'] === 'html') {
            echo ';';
        }?></td>
      </tr>
    <?php endforeach?>
    <?php $pronunciation = $codepoint->pronunciation;
    if ($pronunciation):?>
      <tr>
        <th>Pīnyīn</th>
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
      <tr>
        <th><?=q(array_get($info->properties, $v, $v))?></th>
        <td><?=q($props[$v])?></td>
      </tr>
    <?php endif; endforeach?>
  </tbody>
</table>
