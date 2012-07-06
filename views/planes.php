<?php $title = 'Unicode Planes';
$hDescription = 'Unicode defines 16 planes, in which all the codepoints are separated.';
$canonical = '/planes';
include "header.php";
include "nav.php";
?>
<div class="payload planes">
  <figure>
    <img src="/static/images/unicode-logo-framed.png" alt="a representation of the Unicode logo" width="128" height="128"/>
  </figure>
  <h1><?php e($title)?></h1>
  <ol style="margin-left: 140px">
    <?php foreach ($planes as $plane):?>
      <li><a href="<?php e($router->getUrl($plane))?>"><?php e($plane->name)?></a>
          <small>(U+<?php f('%04X', $plane->first)?> to <?php f('%04X', $plane->last)?>)</small></li>
    <?php endforeach?>
  </ol>
  <p>The Unicode standard arranges the characters in 16 so-called planes of
     a bit more than 65,000 codepoints (2<sup>16</sup> to be precise) each.
     The Unicode standard has thus theoretically place for <em>1,114,112 characters</em>.
     Some planes are still undefined
     and will be filled at a later date. The most used characters go into
     the almost full <a href="<?php e($router->getUrl($planes[0]))?>">Basic
     Multilingual Plane</a>.
  </p>
  <p>The <a href="<?php e($router->getUrl($planes[1]))?>">second plane</a> contains mostly ancient characters, like Egyptian Hieroglyphs,
     and graphic symbols, for example Mahjongg tiles or emoticons. Thirdly the
     <a href="<?php e($router->getUrl($planes[2]))?>">Supplementary Ideographic Plane</a> hosts lots of East Asian characters, that
     didn’t find a place in the Basic Multilingual Plane.
     The third-to-last <a href="<?php $_sspp = array_slice($planes ,-3, 1); e($router->getUrl($_sspp[0]))?>">Supplementary Special Purpose Plane</a>
     is almost completely empty and planned to contain non-character codepoints,
     like control characters, that define the language of a text.
     The last two planes are special purpose planes. Codepoints defined there
     are <em>private</em>, that is, they will never be specified by Unicode and
     can be freely assigned by third-party programs to whatever seems useful.
  </p>
</div>
<?php include "footer.php"?>
