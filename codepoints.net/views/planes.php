<?php $title = __('Unicode Planes');
$hDescription = __('Unicode defines 17 planes, in which all the codepoints are separated.');
$canonical = '/planes';
include "header.php";
include "nav.php";
?>
<div class="payload planes">
  <figure>
    <img src="/static/images/unicode-logo-framed.png" alt="<?php _e('a representation of the Unicode logo')?>" width="128" height="128">
  </figure>
  <h1><?php e($title)?></h1>
  <ol style="margin-left: 140px">
    <?php foreach ($planes as $plane):?>
      <li><a href="<?php e($router->getUrl($plane))?>"><?php e($plane->name)?></a>
          <small><?php printf(__('(U+%04X to U+%04X)'), $plane->first, $plane->last)?></small></li>
    <?php endforeach?>
  </ol>
  <p><?php printf(__('The Unicode standard arranges the characters in 17 so-called planes of
     a bit more than 65,000 codepoints (2<sup>16</sup> to be precise) each.
     The Unicode standard has thus theoretically place for <em>1,114,112 characters</em>.
     Some planes are still undefined
     and will be filled at a later date. The most used characters go into
     the almost full %s.'),
    '<a href="'.q($router->getUrl($planes[0])).'">'.__('Basic Multilingual Plane').'</a>')?>
  </p>
  <p><?php $_sspp = array_slice($planes ,-3, 1);
    printf(__('The %s contains mostly ancient characters, like Egyptian Hieroglyphs,
     and graphic symbols, for example Mahjongg tiles or emoticons. Thirdly the
     %s hosts lots of East Asian characters, that
     didn’t find a place in the Basic Multilingual Plane.
     The third-to-last %s
     is almost completely empty and planned to contain non-character codepoints,
     like control characters, that define the language of a text.
     The last two planes are special purpose planes. Codepoints defined there
     are <em>private</em>, that is, they will never be specified by Unicode and
     can be freely assigned by third-party programs to whatever seems useful.'),
  '<a href="'.q($router->getUrl($planes[1])).'">'.__('second plane').'</a>',
  '<a href="'.q($router->getUrl($planes[2])).'">'.__('Supplementary Ideographic Plane').'</a>',
  '<a href="'.q($router->getUrl($_sspp[0])).'">'.__('Supplementary Special Purpose Plane').'</a>'
)?>
  </p>
</div>
<?php include "footer.php"?>
