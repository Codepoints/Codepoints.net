<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title><?php e($title)?> – Codepoints</title>
    <meta name="author" content="Manuel Strehl"/>
    <meta name="description" content="<?php e(isset($hDescription)? $hDescription : '')?>" />
    <!--[if lt IE 9]>
      <script src="/static/js/html5shiv.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="/static/css/codepoints.css"/>
    <!--[if lt IE 9]>
      <link rel="stylesheet" href="/static/css/ie.css"/>
    <![endif]-->
    <link rel="shortcut icon" type="image/vnd.microsoft.icon" href="/static/images/favicon.ico"/>
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/static/images/icon144.png"/>
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/static/images/icon114.png"/>
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/static/images/icon72.png"/>
    <link rel="apple-touch-icon-precomposed" href="/static/images/icon57.png"/>
    <link rel="search" href="/opensearch.xml" type="application/opensearchdescription+xml" title="Search Codepoints" />
    <link rel="author" href="/humans.txt" />
    <?php if(isset($canonical) && $canonical):?>
      <link rel="canonical" href="http://codepoints.net<?php e($canonical)?>" />
    <?php endif?>
    <?php echo isset($headdata)? $headdata : ''?>
  </head>
  <body>
    <div class="stage">
