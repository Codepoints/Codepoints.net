<?php
/**
 * @var \Codepoints\Unicode\Block $block
 * @var ?\Codepoints\Unicode\Block $prev
 * @var ?\Codepoints\Unicode\Block $next
 */
?>
<link rel="up" href="<?=q(url($block->plane))?>">
<?php if ($prev): ?>
  <link rel="prev" href="<?=q(url($prev))?>">
<?php endif ?>
<?php if ($next): ?>
  <link rel="next" href="<?=q(url($next))?>">
<?php endif ?>

<script type="application/ld+json"><?php
$schema = [
  "@context" => "http://schema.org",
  "@type" => "BreadcrumbList",
  "itemListElement" => [
    [
      "@type" => "ListItem",
      "position" => 1,
      "name" => "Unicode",
      "item" => url('/planes', true),
    ],
    [
      "@type" => "ListItem",
      "position" => 2,
      "name" => $block->plane->name,
      "item" => url($block->plane, true),
    ],
    [
      "@type" => "ListItem",
      "position" => 3,
      "name" => $block->name,
      "item" => url($block, true),
    ],
  ],
];
echo str_replace('</', '&lt;/', json_encode($schema));
unset($schema);
?></script>
