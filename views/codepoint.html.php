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
$hDescription = sprintf(__('%s, codepoint U+%04X %s in Unicode, is located in the block “%s”. It belongs to the %s script and is a %s.'),
    $codepoint->getSafeChar(),
    $codepoint->getId(), $codepoint->getName(), $block->getName(), $info->getLabel('sc', $props['sc']), $info->getLabel('gc', $props['gc']));
$canonical = $router->getUrl($codepoint);
$headdata .= sprintf('<meta name="twitter:site" content="@codepointsnet"/>
<meta name="twitter:url" content="http://codepoints.net%s"/>
<meta name="twitter:title" content="%s"/>
<meta name="twitter:description" content="%s"/>',
q($router->getUrl($codepoint)), q($title), q($hDescription));
if (substr($codepoint->getImage(), -strlen(Codepoint::$defaultImage)) !==
    Codepoint::$defaultImage) {
        $headdata .= '<meta name="twitter:image" content="http://codepoints.net/api/v1/glyph/'.$codepoint->getId('hex').'"/>';
}
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
    e($router->getUrl('search?'.$cat.'='.rawurlencode($props[$cat])));
    echo '">';
    e($info->getLabel($cat, $props[$cat]));
    echo '</a>';
};
?>
<div class="payload codepoint" itemscope="itemscope" itemtype="http://schema.org/StructuredValue/Unicode/CodePoint">
  <figure>
    <span class="fig" itemprop="image"><?php e($codepoint->getSafeChar())?></span>
    <?php if ($codepoint->getId() > 0xFFFF):
        $fonts = $codepoint->getFonts();
        if (count($fonts)):?>
          <datalist id="fonts">
            <?php foreach ($fonts as $font):?>
              <option value="<?php e($font['id'])?>"><?php e($font['font'])?></option>
            <?php endforeach?>
          </datalist>
        <?php endif?>
    <?php endif?>
  </figure>
  <aside>
    <!--h3>Properties</h3-->
    <dl>
      <dt><?php _e('Nº')?></dt>
      <dd><?php e($codepoint->getId())?></dd>
      <dt><?php _e('UTF-8')?></dt>
      <dd><?php e($codepoint->getRepr('UTF-8'))?></dd>
      <?php foreach(array('gc', 'sc', 'bc', 'dt', 'ea') as $cat):?>
        <dt><?php e($info->getCategory($cat))?></dt>
        <dd><a href="<?php e('search?'.$cat.'='.$props[$cat])?>"><?php e($info->getLabel($cat, $props[$cat]))?></a></dd>
      <?php endforeach?>
      <?php if($props['nt'] !== 'None'):?>
      <dt><?php _e('Numeric Value')?></dt>
        <dd><a href="<?php e('search?nt='.$props['nt'])?>"><?php e($info->getLabel('nt', $props['nt']).' '.$props['nv'])?></a></dd>
      <?php endif?>
    </dl>
  </aside>
  <aside class="other codepoint--tools">
    <p>
      <a href="https://twitter.com/share?text=<?php _e(rawurlencode("U+".$codepoint->getId('hex').' '.$codepoint->getName().': '.$codepoint->getSafeChar()))?>&amp;url=<?php echo rawurlencode('http://codepoints.net'.$router->getUrl($codepoint))?>&amp;via=CodepointsNet&amp;hashtags=Unicode" target="_blank" class="button button--hi button--tweet"><i class="icon-twitter"></i> Tweet this codepoint</a>
    </p>
    <p>
      <button type="button" class="button button--hi button--embed" data-link="#tools-embed-<?php _e($codepoint->getId('hex'))?>"><i class="icon-cog"></i> Embed this codepoint</button>
    </p>
    <div style="display:none" id="tools-embed-<?php _e($codepoint->getId('hex'))?>">
      <p><?php _e('Embed this codepoint in your own website by simply
      copy-and-pasting the following HTML snippet:')?></p>
      <pre>&lt;iframe src="http://codepoints.net/U+<?php _e($codepoint->getId('hex'))?>?embed"
        style="width: <span contenteditable="true">200px</span>; height: <span contenteditable="true">26px</span>;
        border: 1px solid #444;">
&lt;/iframe></pre>
      <p><?php _e('If you want, you can freely change width and height to meet
        your needs. The layout will adapt accordingly.')?></p>
    </div>
  </aside>
  <h1 itemprop="name">U+<?php e($codepoint->getId('hex'))?> <?php e($codepoint->getName())?></h1>
  <section class="abstract" itemprop="description">
    <?php include "codepoint/info.php"?>
  </section>
  <section>
    <?php include "codepoint/representations.php"?>
  </section>
<?php if (count($relatives) + count($confusables)):?>
  <section>
    <h2><?php _e('Related Characters')?></h2>
    <?php if (count($relatives)):?>
      <ul class="data">
        <?php foreach ($relatives as $rel):?>
          <li><?php cp($rel)?></li>
        <?php endforeach?>
      </ul>
    <?php endif?>
    <?php if (count($confusables)):?>
      <h3 id="confusables"><?php _e('Confusables')?></h3>
      <ul class="data">
        <?php foreach ($confusables as $rel): ?>
          <li><?php cp($rel)?></li>
        <?php endforeach?>
      </ul>
    <?php endif?>
  </section>
<?php endif?>
  <section>
    <h2><?php _e('Elsewhere')?></h2>
    <ul>
      <li><a href="http://decodeunicode.org/en/U+<?php e($codepoint->getId('hex'))?>">Decode Unicode</a></li>
      <li><a href="http://fileformat.info/info/unicode/char/<?php e($codepoint->getId('hex'))?>/index.htm">Fileformat.info</a></li>
      <li><a href="http://unicode.org/cldr/utility/character.jsp?a=<?php e($codepoint->getId('hex'))?>"><?php _e('Unicode website')?></a></li>
      <li><a href="http://www.unicode.org/cgi-bin/refglyph?24-<?php e($codepoint->getId('hex'))?>"><?php _e('Reference rendering on Unicode.org')?></a></li>
      <?php if (array_key_exists('abstract', $props) && $props['abstract']):?>
        <li><a href="http://en.wikipedia.org/wiki/<?php e(rawurlencode($codepoint->getChar()))?>"><?php _e('Wikipedia')?></a></li>
      <?php endif?>
      <?php if ($props['kDefinition']):?>
        <li><a href="http://www.unicode.org/cgi-bin/GetUnihanData.pl?codepoint=<?php e(rawurlencode($codepoint->getChar()))?>"><?php _e('Unihan Database')?></a></li>
        <li><a href="http://ctext.org/dictionary.pl?if=en&amp;char=<?php e(rawurlencode($codepoint->getChar()))?>"><?php _e('Chinese Text Project')?></a></li>
      <?php endif?>
      <li><a href="http://graphemica.com/<?php e(rawurlencode($codepoint->getChar()))?>">Graphemica</a></li>
      <li><a href="http://www.isthisthingon.org/unicode/index.phtml?glyph=<?php e($codepoint->getId('hex'))?>">The UniSearcher</a></li>
    </ul>
  </section>
  <section>
  <h2><?php _e('Complete Record')?></h2>
    <?php include "codepoint/props.php"?>
  </section>
</div>
<?php include "footer.php"?>
