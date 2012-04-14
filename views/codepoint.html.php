<?php
$title = 'U+' . $codepoint->getId('hex'). ' ' . $codepoint->getName();
$prev = $codepoint->getPrev();
$next = $codepoint->getNext();
$props = $codepoint->getProperties();
$block = $codepoint->getBlock();
$headdata = sprintf('<link rel="up" href="%s"/>', q($router->getUrl($block)));
if ($prev):
    $headdata .= '<link rel="prev" href="' . q($router->getUrl($prev)) . '" />';
endif;
if ($next):
    $headdata .= '<link rel="next" href="' . q($router->getUrl($next)) . '" />';
endif;
$hDescription = sprintf('The Unicode codepoint U+%04X is located in the block “%s”. It belongs to the %s script.',
    $codepoint->getId(), $block->getName(), $info->getLabel('sc', $props['sc']));
include "header.php";
$nav = array();
if ($prev) {
    $nav['prev'] = _cp($prev, 'prev', 'min', 'span');
}
$nav["up"] = _bl($block, 'up', 'min', 'span');
if ($next) {
    $nav['next'] = _cp($next, 'next', 'min', 'span');
}
include "nav.php";
$s = function($cat) use ($router, $info, $props) {
    echo '<a href="';
    e($router->getUrl('search?'.$cat.'='.$props[$cat]));
    echo '">';
    e($info->getLabel($cat, $props[$cat]));
    echo '</a>';
};
?>
<div class="payload codepoint">
  <figure>
    <span class="fig"><?php e($codepoint->getSafeChar())?></span>
  </figure>
  <aside>
    <!--h3>Properties</h3-->
    <dl>
      <?php foreach(array('gc', 'sc', 'bc', 'dt', 'ea') as $cat):?>
        <dt><?php e($info->getCategory($cat))?></dt>
        <dd><a href="<?php e('search?'.$cat.'='.$props[$cat])?>"><?php e($info->getLabel($cat, $props[$cat]))?></a></dd>
      <?php endforeach?>
      <?php if($props['nt'] !== 'None'):?>
        <dt>Numeric Value</dt>
        <dd><a href="<?php e('search?nt='.$props['nt'])?>"><?php e($info->getLabel('nt', $props['nt']).' '.$props['nv'])?></a></dd>
      <?php endif?>
    </dl>
  </aside>
  <h1>U+<?php e($codepoint->getId('hex'))?> <?php e($codepoint->getName())?></h1>
  <section class="abstract">
  <p>
    This codepoint is categorized as <?php $s('gc')?> and belongs to the
    <?php $s('sc')?>
    <?php e($info->getCategory('sc'))?>.
    It was added to Unicode in version
    <?php $s('age')?>.
    The glyph is
    <?php if ($props['dt'] === 'none'):?>
      <a href="<?php e($router->getUrl('search?dt=none'))?>">not a composition</a>.
    <?php else:?>
      a <?php $s('dt')?> composition of the glyphs
      <?php cp($props['dm'], '', 'min')?>.
    <?php endif?>
    The codepoint is located in the block
    <?php bl($block)?> in the
    <?php $plane = $codepoint->getPlane();
    f('<a class="pl" href="%s">%s</a>', $router->getUrl($plane), $plane->name); ?>.
    <?php
    $hasUC = ($props['uc'] && (is_array($props['uc']) || $props['uc']->getId() != $codepoint->getId()));
    $hasLC = ($props['lc'] && (is_array($props['lc']) || $props['lc']->getId() != $codepoint->getId()));
    $hasTC = ($props['tc'] && (is_array($props['tc']) || $props['tc']->getId() != $codepoint->getId()));
    if ($hasUC || $hasLC || $hasTC):?>
        It is related to
        <?php if ($hasUC):?>its uppercase variant <?php cp($props['uc'], '', 'min')?><?php endif?>
        <?php if ($hasLC): if ($hasUC) { echo $hasTC? ', ' : ' and '; }?>
            its lowercase variant <?php cp($props['lc'], '', 'min')?><?php endif?>
        <?php if ($hasTC): if ($hasUC || $hasLC) { echo ' and '; }?>
            its titlecase variant <?php cp($props['tc'], '', 'min')?><?php endif?>.
    <?php endif?>
    In bidirectional context it acts as
    <?php $s('bc')?>
    and is
    <a href="<?php e($router->getUrl('search?bc='.$props['bc'].'&bm='.(int)$props['Bidi_M']))?>"><?php if (! $props['Bidi_M']):?>not <?php endif?>mirrored</a>.
    <?php if (array_key_exists('bmg', $props) && $props['bmg']->getId() != $codepoint->getId()):?>
    Its corresponding mirrored character is <?php cp($props['bmg'], '', 'min')?>.
    <?php endif?>
  </p>
  <p>
    The codepoint has a <?php $s('ea')?>
    <?php e($info->getCategory('ea'))?>.
    <?php $defn = $codepoint->getProp('kDefinition');
      if ($defn):?>
      The Unihan Database defines its glyph as <em><?php
        echo preg_replace_callback('/U\+([0-9A-F]{4,6})/', function($m) {
            $router = Router::getRouter();
            $db = $router->getSetting('db');
            return _cp(Codepoint::getCP(hexdec($m[1]), $db), '', 'min');
        }, $defn);
      ?></em>.
    <?php endif?>
    <?php $pronunciation = $codepoint->getPronunciation();
      if ($pronunciation):?>
      Its Pīnyīn pronunciation is
      <em><?php e($pronunciation)?></em>.
    <?php endif?>
  </p>
  <p>
    In text U+<?php e($codepoint->getId('hex'))?> behaves as <?php $s('lb')?> 
    regarding line breaks. It has type <?php $s('SB')?> for sentence and <?php 
    $s('WB')?> for word breaks. The <?php e($info->getCategory('GCB'))?> property 
    is <?php $s('GCB')?>.
    <?php if($props['nt'] !== 'None'):?>
        The codepoint has a <?php $s('nt')?> numeric value of <?php $s('nv')?>.
    <?php endif?>
  </p>
  <?php if (array_key_exists('abstract', $props) && $props['abstract']):?>
    <p>The <a href="http://en.wikipedia.org/wiki/%<?php e($codepoint->getRepr('UTF-8', '%'))?>">Wikipedia</a>
    has the following information about this codepoint:</p>
    <blockquote>
      <?php echo strip_tags($props['abstract'], '<p><b><strong class="selflink"><strong><em><i><var><sup><sub><tt><ul><ol><li><samp><small><hr><h2><h3><h4><h5><dfn><dl><dd><dt><u><abbr><big><blockquote><br><center><del><ins><kbd>')?>
    </blockquote>
  <?php endif?>
  </section>
  <section>
    <h2>Representations</h2>
    <table class="props">
      <thead>
        <tr>
          <th>System</th>
          <th>Representation</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>Nº</td>
          <td><?php e($codepoint->getId())?></td>
        </tr>
        <tr>
          <th>UTF-8</th>
          <td><?php e($codepoint->getRepr('UTF-8'))?></td>
        </tr>
        <tr>
          <th>UTF-16</th>
          <td><?php e($codepoint->getRepr('UTF-16'))?></td>
        </tr>
        <tr>
          <th>UTF-32</th>
          <td><?php e($codepoint->getRepr('UTF-32'))?></td>
        </tr>
        <tr>
          <th>URL-Quoted</th>
          <td>%<?php e($codepoint->getRepr('UTF-8', '%'))?></td>
        </tr>
        <tr>
          <th>HTML-Escape</th>
          <td>&amp;#x<?php e($codepoint->getId('hex'))?>;</td>
        </tr>
        <?php $alias = $codepoint->getALias();
        foreach ($alias as $a):?>
          <tr>
            <th><?php e($a['type'])?></th>
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
          <th><?php e($info->getCategory($v))?></th>
            <td><?php e($props[$v])?></td>
          </tr>
        <?php endif; endforeach?>
      </tbody>
    </table>
  </section>
<?php $relatives = $codepoint->related();
$confusables = $codepoint->getConfusables();
if (count($relatives) + count($confusables)):?>
  <section>
    <h2>Related Characters</h2>
    <?php if (count($relatives)):?>
      <ul class="data">
        <?php foreach ($relatives as $rel):?>
          <li><?php cp($rel)?></li>
        <?php endforeach?>
      </ul>
    <?php endif?>
    <?php if (count($confusables)):?>
      <h3>Confusables</h3>
      <ul class="data">
        <?php foreach ($confusables as $rel): ?>
          <li><?php cp($rel)?></li>
        <?php endforeach?>
      </ul>
    <?php endif?>
  </section>
<?php endif?>
  <section>
    <h2>Elsewhere</h2>
    <ul>
      <li><a href="http://decodeunicode.org/en/U+<?php e($codepoint->getId('hex'))?>">Decode Unicode</a></li>
      <li><a href="http://fileformat.info/info/unicode/char/<?php e($codepoint->getId('hex'))?>/index.htm">Fileformat.info</a></li>
      <li><a href="http://unicode.org/cldr/utility/character.jsp?a=<?php e($codepoint->getId('hex'))?>">Unicode website</a></li>
      <li><a href="http://www.unicode.org/cgi-bin/refglyph?24-<?php e($codepoint->getId('hex'))?>">Reference rendering on Unicode.org</a></li>
      <li><a href="http://www.unicode.org/cgi-bin/GetUnihanData.pl?codepoint=<?php e(rawurlencode($codepoint->getChar()))?>">Unihan Database</a></li>
      <li><a href="http://graphemica.com/<?php e(rawurlencode($codepoint->getChar()))?>">Graphemica</a></li>
      <li><a href="http://www.isthisthingon.org/unicode/index.phtml?glyph=<?php e($codepoint->getId('hex'))?>">The UniSearcher</a></li>
      <li><a href="http://ctext.org/dictionary.pl?if=en&amp;char=<?php e(rawurlencode($codepoint->getChar()))?>">Chinese Text Project</a></li>
    </ul>
  </section>
  <section>
    <h2>Complete Record</h2>
    <table class="props">
      <thead>
        <tr>
          <th>Property</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        <?php $bools = $info->getBooleanCategories();
        uksort($props, function($a, $b) {
            $n = strcasecmp($a, $b);
            if ($n === 0) {
                return 0;
            }
            $r = array('age', 'na', 'na1', 'blk', 'gc', 'sc', 'bc', 'ccc',
                'dt', 'dm', 'Lower', 'slc', 'lc', 'Upper', 'suc', 'uc',
                'stc', 'tc', 'cf');
            $r2 = array();
            for ($i = 0, $c = count($r); $i < $c; $i++) {
                if ($a === $r[$i]) {
                    if (in_array($b, $r2)) {
                        return 1;
                    } else {
                        return -1;
                    }
                } elseif ($b === $r[$i]) {
                    if (in_array($a, $r2)) {
                        return -1;
                    } else {
                        return 1;
                    }
                } elseif ($a[0] === 'k' && $b[0] === 'k') {
                    if ($a[1] === 'I' && $b[1] !== 'I') {
                        return -1;
                    } elseif ($a[1] !== 'I' && $b[1] === 'I') {
                        return 1;
                    } else {
                        return strcasecmp($a, $b);
                    }
                } else {
                    $r2[] = $r[$i];
                }
            }
            return strcasecmp($a, $b);
        });
        foreach ($props as $k => $v):
            if (! in_array($k, array('cp', 'image', 'abstract')) && ! ($k[0] === 'k' && ! $v)):?>
          <tr class="p_<?php e($k)?>">
            <th><?php e($info->getCategory($k))?> <small>(<?php e($k)?>)</small></th>
            <td>
            <?php if ($v === '' || $v === Null):?>
              <span class="x">—</span>
            <?php elseif (in_array($k, $bools)):?>
              <span class="<?php if ($v):?>y">✔<?php else:?>n">✘<?php endif?></span>
            <?php elseif (is_array($v) || $v instanceof Codepoint):?>
              <?php cp($v, '', 'min') ?>
            <?php elseif ($k === 'scx'):
            foreach(explode(' ', $v) as $sc):?>
                <a href="<?php e($router->getUrl('search?'.$k.'='.$v))?>"><?php e($info->getLabel('sc', $sc))?></a>
            <?php endforeach;
            elseif (in_array($k, array('kCompatibilityVariant', 'kDefinition',
                'kSemanticVariant', 'kSimplifiedVariant',
                'kSpecializedSemanticVariant', 'kTraditionalVariant', 'kZVariant'))):
              echo preg_replace_callback('/U\+([0-9A-F]{4,6})/', function($m) use ($codepoint) {
                if (hexdec($m[1]) === $codepoint->getId()) {
                    return _cp($codepoint, '', 'min');
                }
                $router = Router::getRouter();
                $db = $router->getSetting('db');
                return _cp(Codepoint::getCP(hexdec($m[1]), $db), '', 'min');
              }, $v);
            else:
              $s($k);
            endif?>
            </td>
          </tr>
        <?php endif; endforeach?>
      </tbody>
    </table>
  </section>
</div>
<?php include "footer.php"?>
