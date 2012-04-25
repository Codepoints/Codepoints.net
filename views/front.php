<?php $title = 'Codepoints';
$hDescription = 'Codepoints is a site dedicated to the Unicode standard.';
include "header.php";
include "nav.php";
?>
<div class="payload front">
  <h1><?php e($title)?></h1>
  <form method="get" action="<?php e($router->getUrl('SearchResult'))?>" class="searchform">
    <p><input type="text" name="q" placeholder="Search a Character" />
       <button type="submit"><span>search</span></button></p>
  </form>
  <article>
    <blockquote class="central">
      <p><strong>Codepoint</strong>, <em>n.</em> the position of a character in
         an encoding system.</p>
    </blockquote>
    <p class="action">
      <span class="desc">Start here: <small>Browse one by one through blocks of characters</small></span>
      <a class="button browse" href="<?php e($router->getUrl('basic_latin'))?>">Browse Codepoints</a>
    </p>
    <p class="action">
      <span class="desc">Need help? <small>Answer questions to find matching characters</small></span>
      <a class="button find" href="<?php e($router->getUrl('wizard'))?>">Find my Codepoint</a>
    </p>
    <p class="action">
      <span class="desc">Expert Search! <small>Search for characters with particular properties</small></span>
      <a class="button expert" href="<?php e($router->getUrl('search'))?>">Search Codepoint</a>
    </p>
    <p>This site is dedicated to all the characters, that are defined in
       the <a href="http://unicode.org">Unicode Standard</a>. Theoretically,
       these should be <em>all characters ever used</em>. In practice Unicode
       has <em><?php e($nCPs)?> codepoints</em> defined at the moment, mapping characters
       from <a href="<?php e($router->getUrl('egyptian_hieroglyphs'))?>">Egyptian Hieroglyphs</a>
       to <a href="<?php e($router->getUrl('dingbats'))?>">Dingbats and Symbols</a>.
    <p>All codepoints are arranged in 16 so-called
       <a href="<?php e($router->getUrl('planes'))?>">planes</a>. These planes
       are further divided into several blocks with
       <a href="<?php e($router->getUrl('/basic_latin'))?>">Basic Latin</a>
       being the first one.
       You can browse one by one by starting with the first codepoint,
       <?php cp(Codepoint::getCP(0, $router->getSetting('db')))?> or
       <a href="<?php e($router->getUrl('search'))?>">search</a> for a specific
       character. If you’re not fully sure, try <a href="<?php e($router->getUrl('wizard'))?>">
       “Find My Codepoint”</a>, to narrow down the candidates.
       Or maybe you are more daring and want
       <a href="<?php e($router->getUrl('random'))?>">a random codepoint</a>?
    </p>
    <h2>The <i>(currently defined)</i> Unicode Planes</h2>
    <ol>
      <?php foreach ($planes as $plane):?>
        <li><a href="<?php e($router->getUrl($plane))?>"><?php e($plane->name)?></a></li>
      <?php endforeach?>
    </ol>
  </article>
</div>
<?php include "footer.php"?>
