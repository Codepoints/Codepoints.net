<!DOCTYPE html>
<html lang="<?php e($lang)?>">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php e($title)?> – Codepoints</title>
    <meta name="author" content="Manuel Strehl">
    <meta name="description" content="<?php e(isset($hDescription)? $hDescription : '')?>">
    <!--[if lt IE 9]>
      <script src="/static/js/html5shiv.js!<?php e(CACHE_BUST)?>"></script>
    <![endif]-->
    <link rel="stylesheet" href="/static/css/codepoints.css!<?php e(CACHE_BUST)?>">
    <!--[if lt IE 9]>
      <link rel="stylesheet" href="/static/css/ie.css!<?php e(CACHE_BUST)?>">
    <![endif]-->
    <link rel="shortcut icon" type="image/vnd.microsoft.icon" href="/static/images/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/static/images/icon144.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/static/images/icon114.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/static/images/icon72.png">
    <link rel="apple-touch-icon-precomposed" href="/static/images/icon57.png">
    <link rel="search" href="/opensearch.xml" type="application/opensearchdescription+xml" title="Search Codepoints">
    <link rel="author" href="/humans.txt">
    <link rel="author" href="https://plus.google.com/107008580830183396063?rel=author">
    <link rel="publisher" href="https://plus.google.com/115373008615574082246">
    <?php if(isset($canonical) && $canonical):?>
      <link rel="canonical" href="https://codepoints.net<?php e($canonical)?>">
    <?php endif?>
    <?php echo isset($headdata)? $headdata : ''?>
    <?php if (isset($canonical) && $lang !== 'en'):?>
      <link rel="alternate" hreflang="en" href="https://codepoints.net<?php e($canonical.(strpos($canonical, '?') !== false? '&' : '?').'lang=en')?>">
    <?php endif?>
    <?php if (isset($canonical) && $lang !== 'de'):?>
      <link rel="alternate" hreflang="de" href="https://codepoints.net<?php e($canonical.(strpos($canonical, '?') !== false? '&' : '?').'lang=de')?>">
    <?php endif?>
    <?php if (isset($canonical) && isset($_GET['lang'])):?>
      <link rel="alternate" hreflang="x-default" href="https://codepoints.net<?php e($canonical)?>">
    <?php endif?>
  </head>
  <body>
    <div class="stage">
