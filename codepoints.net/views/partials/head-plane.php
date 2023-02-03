<?php
/**
 * @var \Codepoints\Unicode\Plane $plane
 * @var ?\Codepoints\Unicode\Plane $prev
 * @var ?\Codepoints\Unicode\Plane $next
 */
?>
<link rel="up" href="<?=q(url('planes'))?>">
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
      "name" => $plane->name,
      "item" => url($plane),
    ],
  ],
];
echo str_replace('</', '&lt;/', json_encode($schema));
unset($schema);
?></script>
