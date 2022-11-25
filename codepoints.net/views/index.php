<?php
/**
 * @var int $cp_count
 * @var \Codepoints\Unicode\Codepoint $cp0
 */

include 'partials/header.php'; ?>
<main class="main main--index">
  <h1 aria-label="<?=_q('Codepoints')?>">
    <svg width="100%" viewBox="0 0 100 15" aria-hidden="true" style="overflow:hidden">
      <text x="50" y="12.5" text-anchor="middle" font-size="14.5" style="
        font-family: Literata;
        font-weight: 550;
        fill: white;
        font-variation-settings: &quot;opsz&quot; 50;
        text-transform: uppercase;
        text-shadow: 0 3px 3px #0006;
      ">Codepoints</text>
    </svg>
  </h1>
  <blockquote class="central">
    <p><?=__('<strong>Codepoint</strong>, <em>n.</em> the position of a character in
    an encoding system.')?></p>
  </blockquote>
  <form method="get" action="/search" class="searchform">
    <p><input type="text" name="q" placeholder="<?=_q('Search a Character')?>"
       title="<?=_q('Enter a single character, like “丙” or “A”, or a term that describes the character, like “cyrillic” or “grass”')?>">
       <button type="submit"><?=_q('search')?></button></p>
    <p><small><em><?=_q('For example:')?></em>
      <a href="/search?q=<?=urlencode(_q('heart'))?>"><?=_q('heart')?></a>,
      <a href="/search?q=<?=urlencode(_q('parenthesis'))?>"><?=_q('parenthesis')?></a>,
      <a href="/search?q=<?=urlencode(_q('shavian'))?>"><?=_q('shavian')?></a>,
      <a href="/search?q=<?=urlencode(_q('emoji'))?>"><?=_q('emoji')?></a>
    </small></p>
  </form>
  <p class="action">
    <span class="desc"><?=_q('Start here:')?> <small><?=_q('Browse one by one through blocks of characters')?></small></span>
    <a class="button browse" href="<?=url('basic_latin')?>"><?=_q('Browse Codepoints')?></a>
  </p>
  <p class="action">
    <span class="desc"><?=_q('Need help?')?> <small><?=_q('Answer questions to find matching characters')?></small></span>
    <a class="button find" href="<?=url('search')?>#wizard"><?=_q('Find My Codepoint')?></a>
  </p>
  <p class="action">
    <span class="desc"><?=_q('Expert Search!')?> <small><?=_q('Search for characters with particular properties')?></small></span>
    <a class="button expert" href="<?=url('search')?>"><?=_q('Search Codepoint')?></a>
  </p>
  <section class="bk">
    <h2><?=_q('About this Site')?></h2>
    <p><?php printf(__('Codepoints.net is dedicated to all the characters,
          that are defined in the %s. Theoretically, these should be
          <em>all characters ever used</em>. In practice Unicode has
          <em>%s codepoints</em> defined at the moment, mapping characters
          from %s to %s.'),
      '<a href="http://unicode.org">'.__('Unicode Standard').'</a>',
      $cp_count,
      '<a href="'.url('egyptian_hieroglyphs').'">'.__('Egyptian Hieroglyphs').'</a>',
      '<a href="'.url('dingbats').'">'.__('Dingbats and Symbols').'</a>'
  )?>
    </p>
    <p><?php printf(__('All codepoints are arranged in 17 so-called
      %s. These planes are further divided into several blocks with
      %s being the first one. You can browse one by one by starting with
      the first codepoint, %s or %s for a specific character. If you’re
      not fully sure, try %s, to narrow down the candidates. Or maybe you
      are more daring and want %s?'),
      '<a href="'.url('planes').'">'.__('planes').'</a>',
      '<a href="'.url('basic_latin').'">'.__('Basic Latin').'</a>',
      cp($cp0),
      '<a href="'.url('search').'">'.__('search').'</a>',
      '<a href="'.url('search').'#wizard">'.__('“Find My Codepoint”').'</a>',
      '<a href="'.url('random').'">'.__('a random codepoint').'</a>'
  )?>
    </p>
  </section>
  <section class="bk">
    <h2><?=_q('The 20 most popular code points')?></h2>
    <cp-most-popular></cp-most-popular>
  </section>
  <script type="application/ld+json">
  {
    "@context":"http://schema.org",
    "@type":"WebSite",
    "url":"https://codepoints.net/",
    "name":"Codepoints",
    "alternateName":"All Unicode characters from Hieroglyphs to Dingbats",
    "image":"https://codepoints.net<?= static_url('src/public/images/icon.svg')?>",
    "about": [
        { "@type": "Thing", "name": "Unicode", "sameAs":"https://en.wikipedia.org/wiki/Unicode", },
    ],
    "potentialAction":{
      "@type":"SearchAction",
      "target":"https://codepoints.net/search?q={q}",
      "query-input":"required name=q"
    }
  }
  </script>
</main>
<?php include 'partials/footer.php'; ?>
