<?php
$title = 'Glossary of Common Terms';
$hDescription = 'This glossary explains central terms of the Unicode standard and character encodings in general.';
$nav = array(
  'main' => '<a href="'.$router->getUrl('about').'#main">About this site</a>',
  'find' => '<a href="'.$router->getUrl('about').'#find">Finding Characters</a>',
  'glossary' => '<em class="active">Glossary of Common Terms</em>',
  'unicode' => '<a href="'.$router->getUrl('about').'#unicode">About Unicode</a>',
);
include 'header.php';
include 'nav.php';
?>
<div class="payload static glossary">
  <h1><?php e($title)?></h1>
  <dl>
    <dt>Unicode</dt>
    <dd>Standard</dd>
  </dl>
</div>
<?php include 'footer.php'?>
