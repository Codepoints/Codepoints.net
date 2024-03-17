<?php
/**
 * @var list<\Codepoints\Unicode\Plane> $planes
 */

$nav = [];
$nav['next'] = pl($planes[0], 'next');

include 'partials/header.php'; ?>
<main class="main main--planes">
  <div>
    <figure class="sqfig plfig">
      <svg width="250" height="250"><svg viewBox="194 97 1960 1960" width="100%" height="100%"><use xlink:href="<?=static_url('images/unicode-logo-framed.svg')?>#unicode"/></svg></svg>
    </figure>
  </div>
  <h1><?=q($title)?></h1>
  <section>
    <p><?php printf(
      __('The Unicode standard arranges the characters in 17 so-called planes of a bit more than 65,000 codepoints (2<sup>16</sup> to be precise) each.').' '.
      __('It has thus theoretically place for <em>1,114,112 characters</em>.').' '.
      __('Some planes are still undefined and will be filled at a later date.').' '.
      __('The most common characters live in the almost full %s.'),
      '<a href="'.q(url($planes[0])).'" rel="child">'.__('Basic Multilingual Plane').'</a>')?>
    </p>
    <p><?php printf(
      __('The %s contains mostly ancient characters, like Egyptian Hieroglyphs, and graphic symbols, for example Mahjongg tiles or emoticons.').' '.
      __('Thirdly the %s hosts lots of East Asian characters, that didn’t find a place in the Basic Multilingual Plane.').' '.
      __('The third-to-last %s is almost completely empty and planned to contain non-character codepoints, like control characters, that define the language of a text.').' '.
      __('The last two planes are special purpose planes.').' '.
      __('Codepoints defined there are <em>private</em>, that is, they will never be specified by Unicode and can be freely assigned by third-party programs to whatever seems useful.'),
      '<a href="'.q(url($planes[1])).'" rel="child">'.__('second plane').'</a>',
      '<a href="'.q(url($planes[2])).'" rel="child">'.__('Supplementary Ideographic Plane').'</a>',
      '<a href="'.q(url($planes[14])).'" rel="child">'.__('Supplementary Special Purpose Plane').'</a>'
  )?>
    </p>
  </section>
  <ol class="tiles">
    <?php foreach ($planes as $plane):?>
      <li><?=pl($plane, 'child')?></li>
    <?php endforeach?>
  </ol>
</main>
<?php include 'partials/footer.php'; ?>
