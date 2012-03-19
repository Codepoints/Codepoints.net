<?php
$title = 'U+' . $codepoint->getId('hex');
$headdata = '';
$prev = $codepoint->getPrev();
$next = $codepoint->getNext();
$props = $codepoint->getProperties();
if ($prev):
    $headdata .= '<link href="' . $prev . '" rel="prev">';
endif;
if ($next):
    $headdata .= '<link href="' . $next . '" rel="next">';
endif;
include "header.php";
?>
<div class="payload codepoint">
  <nav>
    <ul>
      <li class="prev">
        <?php if ($prev): cp($prev, 'prev', 'min'); endif?>
      </li>
      <li class="up">
        <?php $block = $codepoint->getBlock();
        f('<a class="bl" rel="up" href="%s"><img src="static/images/blocks.min/%s.png" alt="" width="16" height="16" /> %s</a>', $router->getUrl($block),
          str_replace(' ', '_', $block->getName()),
          $block->getName()); ?>
      </li>
      <li class="next">
        <?php if ($next): cp($next, 'next', 'min'); endif?>
      </li>
    </ul>
  </nav>
  <h1><img src="data:<?php e($codepoint->getImage())?>" alt="" width="16" height="16" /> <?php e($title)?><br/>
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
      <dd><?php e($props['age'])?></dd>
      <dt>Script</dt>
      <dd><?php e($props['sc'])?></dd>
      <dt>General Category</dt>
      <dd><?php e($props['gc'])?></dd>
      <dt>Bidi Class</dt>
      <dd><?php e($props['bc'])?></dd>
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
        <dd>
          <?php e($props['nv'])?>
        </dd>
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
      <dd><?php $block = $codepoint->getBlock();
          f('<a class="bl" href="%s">%s</a>', $router->getUrl($block), $block->getName());
      ?></dd>
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
