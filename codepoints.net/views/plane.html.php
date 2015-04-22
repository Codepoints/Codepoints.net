<?php
$title = $plane->getName();
$blocks = $plane->getBlocks();
$prev = $plane->getPrev();
$next = $plane->getNext();
$hDescription = sprintf(__('The Unicode plane %s contains %s blocks and spans codepoints from U+%04X to U+%04X.'),
    $plane->getName(), count($blocks), $plane->first, $plane->last);
$canonical = $router->getUrl($plane);
/* add breadcrumbs as linked data: Unicode > Plane */
$headdata = '<script type="application/ld+json">{"@context": "http://schema.org","@type": "BreadcrumbList","itemListElement":[
{"@type":"ListItem","position":1,"item":{"@id":"https://codepoints.net/planes","name":"Unicode"}},
{"@type":"ListItem","position":2,"item":{"@id":"'.q($router->getUrl($plane)).'","name":"'.q($plane->name).'"}}
]}</script>';
include "header.php";
$nav = array();
if ($prev) {
    $nav["prev"] = '<a rel="prev" href="'.q($router->getUrl($prev)).'">'.q($prev->name).'</a>';
}
$nav["up"] = '<a rel="up" href="'.q($router->getUrl('planes')).'">Unicode</a>';
if ($next) {
    $nav["next"] = '<a rel="next" href="'.q($router->getUrl($next)).'">'.q($next->name).'</a>';
}
include "nav.php";
?>
<div class="payload plane">
  <h1><?php e($title);?></h1>
  <p><?php printf(__('Plane from U+%04X to U+%04X.'), $plane->first, $plane->last)?></p>
  <?php if (count($blocks)):?>
    <h2><?php _e('Blocks in this plane')?></h2>
    <ol>
      <?php foreach ($blocks as $b):?>
        <li><?php bl($b)?> <small><?php $l = $b->getBlockLimits(); printf(__('(U+%04X to U+%04X)'), $l[0], $l[1])?></small></li>
      <?php endforeach?>
    </ol>
  <?php else:?>
    <p class="info"><?php _e('There are no blocks defined in this plane.')?></p>
  <?php endif?>
</div>
<?php include "footer.php"?>
