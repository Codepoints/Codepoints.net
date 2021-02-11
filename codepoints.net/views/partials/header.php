<!DOCTYPE html>
<html lang="<?=q($lang)?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=q($title)?> – Codepoints</title>
<?php if(isset($page_description)): ?>
    <meta name="description" content="<?=q($hDescription)?>">
<?php endif ?>
    <link rel="icon" href="/favicon.ico">
    <link rel="icon" href="/static/images/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="search" href="/opensearch.xml" type="application/opensearchdescription+xml" title="Search Codepoints">
    <link rel="author" href="/humans.txt">
  </head>
  <body>
