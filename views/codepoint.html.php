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
include "header.php";
$nav = array();
if ($prev) {
    $nav['prev'] = _cp($prev, 'prev', 'min');
}
$nav["up"] = _bl($block);
if ($next) {
    $nav['next'] = _cp($next, 'next', 'min');
}
include "nav.php";
?>
<div class="payload codepoint">
  <h1><img src="data:<?php e($codepoint->getImage())?>" alt="" width="16" height="16" /> U+<?php e($codepoint->getId('hex'))?>
    <?php e($codepoint->getName())?></h1>
  <section>
    <h2>Representations</h2>
    <dl>
      <dt>Nº</dt>
      <dd><?php e($codepoint->getId())?></dd>
      <dt>Your System</dt>
      <dd><?php if ($props['gc'][0] === 'C'):?>
          <span class="Cc">&lt;control&gt;</span>
      <?php else:?>
          <span>&#<?php e($codepoint->getId())?>;</span>
      <?php endif?></dd>
      <dt>UTF-8</dt>
      <dd><?php e($codepoint->getRepr('UTF-8'))?></dd>
      <dt>UTF-16</dt>
      <dd><?php e($codepoint->getRepr('UTF-16'))?></dd>
      <dt>UTF-32</dt>
      <dd><?php e($codepoint->getRepr('UTF-32'))?></dd>
      <?php $alias = $codepoint->getALias();
      foreach ($alias as $a):?>
        <dt><?php e($a['type'])?></dt>
        <dd><?php if ($a['type'] === 'html') {
              echo '&amp;';
          }
          e($a['name']);
          if ($a['type'] === 'html') {
              echo ';';
          }?></dd>
      <?php endforeach?>
      <?php $pronunciation = $codepoint->getPronunciation();
      if ($pronunciation):?>
        <dt>Pronunciation</dt>
        <dd><?php e($pronunciation)?></dd>
      <?php endif?>
    </dl>
  </section>
  <section>
    <h2>Properties</h2>
    <dl>
      <dt>Unicode version</dt>
      <dd><a href="<?php e('search?age='.$props['age'])?>"><?php e($props['age'])?></a></dd>
      <dt>Script</dt>
      <dd><a href="<?php e('search?sc='.$props['sc'])?>"><?php e($info->getLabel('sc', $props['sc']))?></a></dd>
      <dt>General Category</dt>
      <dd><a href="<?php e('search?gc='.$props['gc'])?>"><?php e($info->getLabel('gc', $props['gc']))?></a></dd>
      <dt>Bidi Class</dt>
      <dd><a href="<?php e('search?bc='.$props['bc'])?>"><?php e($info->getLabel('bc', $props['bc']))?></a></dd>
      <?php if ($defn = $codepoint->getProp('kDefinition')):?>
        <dt>Definition</dt>
        <dd><?php
          echo preg_replace_callback('/U\+([0-9A-F]{4,6})/', function($m) {
              $router = Router::getRouter();
              $db = $router->getSetting('db');
              return _cp(new Codepoint(hexdec($m[1]), $db), '', 'min');
          }, $defn);
        ?></dd>
      <?php endif?>
      <?php if($props['nt'] !== 'None'):?>
        <dt>Numeric Value</dt>
        <dd><a href="<?php e('search?nt='.$props['nt'])?>"><?php e($info->getLabel('nt', $props['nt']).': '.$props['nv'])?></a></dd>
      <?php endif?>
    </dl>
  </section>
  <section>
    <h2>Relations</h2>
    <dl>
      <dt>Plane</dt>
      <dd><?php $plane = $codepoint->getPlane();
          f('<a class="pl" href="%s">%s</a>', $router->getUrl($plane), $plane->name);
      ?></dd>
      <dt>Block</dt>
      <dd><?php bl($block)?></dd>
      <?php if($props['uc'] && (is_array($props['uc']) ||
               $props['uc']->getId() != $codepoint->getId())):?>
        <dt>Uppercase</dt>
        <dd>
          <?php cp($props['uc'])?>
        </dd>
      <?php endif?>
      <?php if($props['lc'] && (is_array($props['lc']) ||
               $props['lc']->getId() != $codepoint->getId())):?>
        <dt>Lowercase</dt>
        <dd>
          <?php cp($props['lc'])?>
        </dd>
      <?php endif?>
      <?php if($props['tc'] && (is_array($props['tc']) ||
               $props['tc']->getId() != $codepoint->getId())):?>
        <dt>Titlecase</dt>
        <dd>
          <?php cp($props['tc'])?>
        </dd>
      <?php endif?>
      <?php if($props['dm'] && (is_array($props['dm']) ||
               $props['dm']->getId() != $codepoint->getId())):?>
        <dt>Decomposition</dt>
        <dd>
          <?php cp($props['dm'])?>
        </dd>
      <?php endif?>
    </dl>
  </section>
  <section>
    <h2>Elsewhere</h2>
    <ul>
      <li><a href="http://decodeunicode.org/en/U+<?php e($codepoint->getId('hex'))?>">Decode Unicode</a></li>
      <li><a href="http://fileformat.info/info/unicode/char/<?php e($codepoint->getId('hex'))?>/index.htm">Fileformat.info</a></li>
      <li><a href="http://www.unicode.org/cgi-bin/refglyph?24-<?php e($codepoint->getId('hex'))?>">Reference rendering on Unicode.org</a></li>
    </ul>
  </section>
  <!--table>
    <tbody>
      <?php foreach ($props as $k => $v):
            if ($v !== NULL && $v !== '' && $k !== 'cp'):?>
        <tr class="p_<?php e($k)?>">
          <th><?php e($info->getCategory($k))?></th>
          <td>
            <?php e($v)?>
          </td>
        </tr>
      <?php endif; endforeach?>
    </tbody>
  </table-->
</div>
<?php include "footer.php"?>
