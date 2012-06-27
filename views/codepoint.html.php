<?php
$title = 'U+' . $codepoint->getId('hex'). ' ' . $codepoint->getName();
$prev = $codepoint->getPrev();
$next = $codepoint->getNext();
$props = $codepoint->getProperties();
$block = $codepoint->getBlock();
$relatives = $codepoint->related();
$confusables = $codepoint->getConfusables();
$headdata = sprintf('<link rel="up" href="%s"/>', q($router->getUrl($block)));
if ($prev):
    $headdata .= '<link rel="prev" href="' . q($router->getUrl($prev)) . '" />';
endif;
if ($next):
    $headdata .= '<link rel="next" href="' . q($router->getUrl($next)) . '" />';
endif;
$hDescription = sprintf('The Unicode codepoint U+%04X is located in the block “%s”. It belongs to the %s script.',
    $codepoint->getId(), $block->getName(), $info->getLabel('sc', $props['sc']));
$canonical = $router->getUrl($codepoint);
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
    <span class="fig"<?php
    if ($codepoint->getId() > 0xFFFF):
        $fonts = $codepoint->getFonts();?>
        data-fonts="<?php e(join(',', $fonts))?>"
    <?php endif; ?>><?php e($codepoint->getSafeChar())?></span>
  </figure>
  <aside>
    <!--h3>Properties</h3-->
    <dl>
      <dt>Nº</dt>
      <dd><?php e($codepoint->getId())?></dd>
      <dt>UTF-8</dt>
      <dd><?php e($codepoint->getRepr('UTF-8'))?></dd>
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
    <?php include "codepoint/info.php"?>
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
          <th>Nº</th>
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
<?php if (count($relatives) + count($confusables)):?>
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
      <h3 id="confusables">Confusables</h3>
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
      <?php if (array_key_exists('abstract', $props) && $props['abstract']):?>
        <li><a href="http://en.wikipedia.org/wiki/<?php e(rawurlencode($codepoint->getChar()))?>">Wikipedia</a></li>
      <?php endif?>
      <?php if ($props['kDefinition']):?>
        <li><a href="http://www.unicode.org/cgi-bin/GetUnihanData.pl?codepoint=<?php e(rawurlencode($codepoint->getChar()))?>">Unihan Database</a></li>
        <li><a href="http://ctext.org/dictionary.pl?if=en&amp;char=<?php e(rawurlencode($codepoint->getChar()))?>">Chinese Text Project</a></li>
      <?php endif?>
      <li><a href="http://graphemica.com/<?php e(rawurlencode($codepoint->getChar()))?>">Graphemica</a></li>
      <li><a href="http://www.isthisthingon.org/unicode/index.phtml?glyph=<?php e($codepoint->getId('hex'))?>">The UniSearcher</a></li>
    </ul>
  </section>
  <section>
    <h2>Complete Record</h2>
    <?php include "codepoint/props.php"?>
  </section>
</div>
<?php include "footer.php"?>
