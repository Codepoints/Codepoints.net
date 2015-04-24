<h2><?php _e('Representations')?></h2>
<table class="props representations">
  <thead>
    <tr>
      <th><?php _e('System')?></th>
      <th><?php _e('Representation')?></th>
    </tr>
  </thead>
  <tbody>
    <tr class="primary">
      <th><?php _e('Nº')?></th>
      <td class="repr-number"><?php e($codepoint->getId())?></td>
    </tr>
    <tr class="primary">
      <th><?php _e('UTF-8')?></th>
      <td><?php e($codepoint->getRepr('UTF-8'))?></td>
    </tr>
    <tr class="primary">
      <th><?php _e('UTF-16')?></th>
      <td><?php e($codepoint->getRepr('UTF-16'))?></td>
    </tr>
    <tr>
      <th><?php _e('UTF-32')?></th>
      <td><?php e($codepoint->getRepr('UTF-32'))?></td>
    </tr>
    <tr>
      <th><?php _e('URL-Quoted')?></th>
      <td>%<?php e($codepoint->getRepr('UTF-8', '%'))?></td>
    </tr>
    <tr>
      <th><?php _e('HTML-Escape')?></th>
      <td>&amp;#x<?php e($codepoint->getId('hex'))?>;</td>
    </tr>
    <?php
    ini_set('mbstring.substitute_character', "none");
    $moji = mb_convert_encoding($codepoint->getSafeChar(), 'UTF-8', 'ISO-8859-1');
    if ($moji): ?>
      <tr>
        <th title="<?php _e('approx. ISO-8859-1, Latin 1, “us-ascii”, ...')?>"><?php _e('Wrong windows-1252 Mojibake')?></th>
        <td><?php echo $moji; ?></td>
      </tr>
    <?php endif ?>
<?php $alias = $codepoint->getALias();
$typeMap = array(
    'abbreviation' => __('abbreviation'),
    'alias' => __('alias'),
    'alternate' => __('alternate'),
    'control' => __('control'),
    'correction' => __('correction'),
    'digraph' => __('digraph'),
    'figment' => __('figment'),
    'html' => __('HTML-Escape'),
    'latex' => '<span class="latex">L<sup>a</sup>T<sub>e</sub>X</span>',
);
    foreach ($alias as $a):?>
      <tr<?php if (in_array($a['type'], array('html', 'abbreviation', 'alias'))):?>
         class="primary"
        <?php endif?>>
        <th><?php if (array_key_exists($a['type'], $typeMap)) {
            echo $typeMap[$a['type']];
        } elseif (substr($a['type'], 0, 4) === 'enc:') {
            e(sprintf(__('Encoding: %s (hex bytes)'),
                strtoupper(substr($a['type'], 4))));
        } else {
            e($a['type']);
        }?></th>
        <td><?php if ($a['type'] === 'html') {
            echo '&amp;';
        }
        e($a['alias']);
        if ($a['type'] === 'html') {
            echo ';';
        }?></td>
      </tr>
    <?php endforeach?>
    <?php $pronunciation = $codepoint->getPronunciation();
    if ($pronunciation):?>
      <tr>
        <th>Pīnyīn</th>
        <td><?php e($pronunciation)?></td>
      </tr>
    <?php endif?>
    <?php foreach (array('kIRG_GSource', 'kIRG_HSource', 'kIRG_JSource',
    'kIRG_KPSource', 'kIRG_KSource', 'kIRG_MSource', 'kIRG_TSource',
    'kIRG_USource', 'kIRG_VSource', 'kBigFive', 'kCCCII', 'kCNS1986',
    'kCNS1992', 'kEACC', 'kGB0', 'kGB1', 'kGB3', 'kGB5', 'kGB7', 'kGB8',
    'kHKSCS', 'kIBMJapan', 'kJis0', 'kJIS0213', 'kJis1', 'kKPS0', 'kKPS1',
    'kKSC0', 'kKSC1', 'kMainlandTelegraph', 'kPseudoGB1',
    'kTaiwanTelegraph', 'kXerox') as $v):
        if ($props[$v]):?>
      <tr>
        <th><?php e(ltrim($info->getCategory($v), 'k'))?></th>
        <td><?php e($props[$v])?></td>
      </tr>
    <?php endif; endforeach?>
  </tbody>
</table>
