<?php include 'partials/header.php'; ?>
<main class="main main--scripts">

  <h1><?=_q('Browse Codepoints by Script')?></h1>
  <section class="bk">
  <p><?=_q('Scripts used around the world. Drag the globe to rotate it.
  Click on a country to see scripts used there.')?></p>
    <div id="space">
      <svg xmlns="http://www.w3.org/2000/svg" id="earth"
           width="100px" viewBox="0 0 800 800">
        <defs>
          <radialGradient id="reflect"
            r="75%" cx="35%" cy="20%">
            <stop stop-color="white" stop-opacity=".67" offset="0" />
            <stop stop-color="white" stop-opacity="0.0" offset=".3" />
            <stop stop-color="white" stop-opacity="0.0" offset=".8" />
            <stop stop-color="black" stop-opacity="0.2" offset="1" />
          </radialGradient>
          <desc><?=_q('This is an interactive graphic of the world. You need an
                SVG-enabled browser to use it. (SVG-enabled browsers are
                Firefox, Opera, Google Chrome, Safari, and IE 9 or higher.)')?></desc>
        </defs>
        <circle cx="50%" cy="50%" r="50%" style="cursor: move" />
        <circle id="athmo" cx="50%" cy="50%" r="50%" style="pointer-events: none; fill: url(#reflect)" />
      </svg>
    </div>
  </section>
  <section class="bk">
    <dl id="sclist">
      <?php foreach ($scripts as $sc): ?>
        <dt id="<?=q($sc['iso'])?>" class="sc_<?=q($sc['iso'])?>"><a href="#<?=q($sc['iso'])?>"><?=q(str_replace('_', ' ', $sc['name']))?></a></dt>
        <dd>
          <p><?php printf(__('%s%s%d%s characters%s are encoded in this script.'),
            '<a rel="nofollow" href="/search?sc='.q($sc['iso']).'">',
            '<span class="nchar">', $sc['count'], '</span>',
            '</a>')?></p>
          <?php if ($sc['abstract']): ?>
            <blockquote cite="<?=q($sc['src'])?>" class="sc__abstract"><?=$sc['abstract']?></blockquote>
          <?php endif ?>
        </dd>
      <?php endforeach?>
    </dl>
  </section>

</main>
<?php include 'partials/footer.php'; ?>
