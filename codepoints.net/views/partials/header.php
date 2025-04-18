<?php
/**
 * @var string $lang
 * @var string $title
 * @var ?string $page_description
 * @var string $view
 * @var ?string $head_extra
 */
?>
<!DOCTYPE html>
<html lang="<?=q($lang)?>" dir="ltr" class="<?= array_key_exists('embed', $_GET)? 'embed' : '' ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title><?=q($title)?> – Codepoints</title>
    <script>document.documentElement.dataset.scheme = localStorage.getItem('scheme') || (window.matchMedia('(prefers-color-scheme: dark)').matches? 'dark' : 'light');</script>
<?php if(isset($page_description)): ?>
    <meta name="description" content="<?=q($page_description)?>">
<?php endif ?>
    <meta name="theme-color" content="#660000">
    <link rel="icon" href="/favicon.ico">
    <link rel="icon" href="<?=static_url('src/images/icon.svg')?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="mask-icon" href="<?=static_url('src/images/safari-pinned-tab.svg')?>" color="#990000">
    <link rel="search" href="/opensearch.xml" type="application/opensearchdescription+xml" title="Search Codepoints">
    <link rel="author" href="/humans.txt">
    <link rel="preload" href="<?= static_url('src/fonts/Literata.woff2') ?>" as="font" crossOrigin="anonymous">
    <link rel="preload" href="<?= static_url('src/fonts/Literata-Italic.woff2') ?>" as="font" crossOrigin="anonymous">
    <link rel="stylesheet" href="<?= static_url('src/css/main.css') ?>" id="main-css">
    <link rel="stylesheet" media="print" href="<?= static_url('src/css/print.css') ?>">
    <?php include 'head-multilang.php' ?>
    <?php if (isset($head_extra)) { echo $head_extra; } ?>
  </head>
  <body>
    <div data-barba="wrapper">
      <div data-barba="container">
        <?php if (! isset($no_navigation) || ! $no_navigation) { include 'main-navigation.php'; } ?>
