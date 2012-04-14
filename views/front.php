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
      <p><strong>Codepoint</strong>, <em>n.</em> the position of a character in an encoding system.</p>
    </blockquote>
    <p>All codepoints are arranged in 16 so-called
       <a href="<?php e($router->getUrl('planes'))?>">planes</a>. These planes
       are further divided in several blocks with
       <a href="<?php e($router->getUrl('/basic_latin'))?>">Basic Latin</a>
       being the first one.
       You can browse one by one by starting with the first codepoint,
       <a href="<?php e($router->getUrl('/U+0000'))?>">U+0000</a>.
       Or maybe you are more daring and want
       <a href="<?php e($router->getUrl('random'))?>">a random codepoint</a>?
    </p>
    <h2>All Unicode Planes</h2>
    <p><i>(as currently defined)</i></p>
    <ol>
      <?php foreach ($planes as $plane):?>
        <li><a href="<?php e($router->getUrl($plane))?>"><?php e($plane->name)?></a></li>
      <?php endforeach?>
    </ol>
  </article>
</div>
<?php include "footer.php"?>
