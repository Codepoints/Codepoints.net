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
      <p><strong>Codepoint</strong>, <em>n.</em> a position of a character in an encoding system.</p>
    </blockquote>
    <p>The <a href="<?php e($router->getUrl('planes'))?>">Unicode Planes</a>
       or maybe you are more daring and want <a href="<?php e($router->getUrl('random'))?>">a random codepoint</a>?
    </p>
    <ol>
      <?php foreach ($planes as $plane):?>
        <li><a href="<?php e($router->getUrl($plane))?>"><?php e($plane->name)?></a></li>
      <?php endforeach?>
    </ol>
  </article>
</div>
<?php include "footer.php"?>
