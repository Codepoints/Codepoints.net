<?php
$title = ($date === $today)? 'Codepoint of the Day' : 'Codepoint from '.$date;
$hDescription = 'Codepoints.net presents every day a new character from Unicode. '.($date === $today? 'Today' : $date).': U+'.$codepoint->getId('hex').', '.$description;
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
$nav = array();
if ($prev) {
    $nav['prev'] = _cp($prev, 'prev', 'min', 'span');
}
$nav["up"] = _bl($block, 'up', 'min', 'span');
if ($next) {
    $nav['next'] = _cp($next, 'next', 'min', 'span');
}
$canonical = '/codepoint_of_the_day?date='.$date;
include "header.php";
include "nav.php";
$s = function($cat) use ($router, $info, $props) {
    echo '<a href="';
    e($router->getUrl('search?'.$cat.'='.$props[$cat]));
    echo '">';
    e($info->getLabel($cat, $props[$cat]));
    echo '</a>';
};
?>
<div class="payload dailycp codepoint">
  <aside class="other">
    <h2>Codepoints of the Day</h2>
    <div id="ucotd_cal" data-date="<?php e($date)?>"></div>
  </aside>
  <figure>
    <span class="fig"><?php e($codepoint->getSafeChar())?></span>
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
  <h2><?php e($title)?></h2>
  <h1>U+<?php e($codepoint->getId('hex'))?> <?php e($codepoint->getName())?></h1>
  <section class="abstract">
    <p><strong><a href="<?php e($router->getUrl($codepoint))?>">View full description of this codepoint.</a></strong></p>
    <?php include "codepoint/info.php"?>
  </section>
  </div>
<?php
$footer_scripts = array(
    "/static/js/dailycp.js"
);
include "footer.php"?>
