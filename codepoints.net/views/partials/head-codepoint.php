<?php if ($block): ?>
  <link rel="up" href="<?=q(url($block))?>">
<?php endif ?>
<?php if ($prev): ?>
  <link rel="prev" href="<?=q(url($prev))?>">
<?php endif ?>
<?php if ($next): ?>
  <link rel="next" href="<?=q(url($next))?>">
<?php endif ?>

<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@codepointsnet">
<meta name="twitter:url" content="https://codepoints.net/<?=q((string)$codepoint)?>">
<meta name="twitter:title" content="<?=q($title)?>">
<meta name="twitter:description" content="<?=q($page_description)?>">
<meta name="twitter:image" content="https://codepoints.net/api/v1/glyph/<?=sprintf('%04X', $codepoint->id)?>">

<meta property="og:site_name" content="Codepoints.net">
<meta property="og:type" content="article">
<meta property="og:title" content="<?=q($title)?>">
<meta property="og:description" content="<?=q($page_description)?>">
<meta property="og:image" content="https://codepoints.net/api/v1/glyph/<?=sprintf('%04X', $codepoint->id)?>">

<link rel="alternate" type="application/json+oembed" href="https://codepoints.net/api/v1/oembed?url=https%3A%2F%2Fcodepoints.net<?=q(url($codepoint))?>&amp;format=json">
<link rel="alternate" type="text/xml+oembed" href="https://codepoints.net/api/v1/oembed?url=https%3A%2F%2Fcodepoints.net<?=q(url($codepoint))?>&amp;format=xml">

<script type="application/ld+json"><?php
$schema = [
    "@context" => "http://schema.org",
    "@type" => "BreadcrumbList",
    "itemListElement" => [
        [
          "@type" => "ListItem",
          "position" => 1,
          "name" => "Unicode",
          "item" => "https://codepoints.net/planes",
        ],
    ],
];
if ($plane):
$schema['itemListElement'][] = [
    "@type" => "ListItem",
    "position" => 2,
    "name" => $plane->name,
    "item" => url($plane),
];
endif;
if ($block):
$schema['itemListElement'][] = [
    "@type" => "ListItem",
    "position" => 3,
    "name" => $block->name,
    "item" => url($block),
];
endif;
$schema['itemListElement'][] = [
    "@type" => "ListItem",
    "position" => 4,
    "name" => $title,
    "item" => url($codepoint),
];
echo str_replace('</', '&lt;/', json_encode($schema));
unset($schema);
?></script>
