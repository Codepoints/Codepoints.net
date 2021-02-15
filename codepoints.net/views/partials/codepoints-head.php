<link rel="up" href="<?=q(url($block))?>">
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

<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem", "position":1,"item":{"@id":"https://codepoints.net/planes","name":"Unicode"}
        },
        {
            "@type":"ListItem","position":2,"item":{"@id":"<?=q(url($plane))?>","name":"<?=q($plane->name)?>"}
        },
        {
            "@type":"ListItem","position":3,"item":{"@id":"<?=q(url($block))?>","name":"<?=q($block->name)?>"}
        },
        {
            "@type":"ListItem","position":4,"item":{"@id":"<?=q(url($codepoint))?>","name":"<?=q($title)?>"}
        }
    ]
}</script>
