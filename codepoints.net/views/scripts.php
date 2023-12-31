<?php
/**
 * @var list<Array{"iso": string, "name": string, "abstract": string, "src": string, "count": int}> $scripts
 */

include 'partials/header.php'; ?>
<main class="main main--scripts">

  <h1><?=_q('Browse Codepoints by Script')?></h1>
  <section class="bk">
    <p><?=_q('See writing scripts and systems how they are used around the world.')?>
       <?=_q('Click on a script name for additional information.')?>
       <?php #=_q('Drag the globe to rotate it.')?>
       <?php #=_q('Click on a country to see scripts used there.')?>
    </p>
    <div id="space" hidden>
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
        </defs>
        <circle cx="50%" cy="50%" r="50%" style="cursor: move" />
        <circle id="athmo" cx="50%" cy="50%" r="50%" style="pointer-events: none; fill: url(#reflect)" />
      </svg>
    </div>
  </section>
  <section class="bk">
    <ul class="bulletless">
      <?php foreach ($scripts as $sc): ?>
        <li>
          <details id="<?=q($sc['iso'])?>">
            <summary>
              <?=q(str_replace('_', ' ', $sc['name']))?><span class="visually-hidden">: </span>
              <span class="badge"><?=$sc['count']?><span class="visually-hidden"> <?=_q('code points')?></span></span>
            </summary>
            <p><?php printf(__('%s%s%d%s characters%s are encoded in this script.'),
              '<a rel="nofollow" href="/search?sc='.q($sc['iso']).'">',
              '<span class="nchar">', $sc['count'], '</span>',
              '</a>')?></p>
            <?php if ($sc['abstract']): ?>
              <blockquote cite="<?=q($sc['src'])?>" class="sc__abstract"><?=$sc['abstract']?></blockquote>
            <?php endif ?>
          </details>
        </li>
      <?php endforeach?>
    </ul>
  </section>
  <script>
(function() {
document.querySelectorAll('details[id] > summary').forEach(node => {
  const id = node.parentNode.id;
  const a = document.createElement('a');
  a.href = '#'+id;
  a.classList.add('direct-link');
  a.textContent = '¶';
  a.ariaLabel = '<?=_q("direct link to this script")?>';
  a.addEventListener('click', () => node.parentNode.open = true);
  node.appendChild(a);
});
const hash = location.hash.replace(/^#/, '');
if (hash) {
  const target = document.getElementById(hash);
  if (target && ('open' in target)) {
    target.open = true;
  }
}
})();
  </script>

</main>
<?php include 'partials/footer.php'; ?>
