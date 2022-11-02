      </div>
    </div>
    <footer class="page-footer">
      <p><?=_q('This is Codepoints.net, a site dedicated to all things characters, letters and Unicode.')?>
         <?php printf(__('Currently displaying Unicode version %s.'), '<strong>'.UNICODE_VERSION.'</strong>')?>
         <a href="<?=q(url('about'))?>"><?=_q('Read more about this site.')?></a></p>
      <nav>
        <ul>
          <li><a href="https://twitter.com/CodepointsNet">
            <?=_q('Twitter')?></a></li>
          <li><a href="https://blog.codepoints.net/">
            <?=_q('Blog')?></a></li>
          <li><a href="<?=q(url('search'))?>" rel="search">
            <?=_q('Search')?></a></li>
          <li><a href="<?=q(url('scripts'))?>">
            <?=_q('Scripts')?></a></li>
          <li><a rel="nofollow" href="<?=q(url('random'))?>">
            <?=_q('Random')?></a></li>
          <li><a href="<?=q(url('about'))?>#this_site">
            <?=_q('About')?></a></li>
        </ul>
      </nav>
    </footer>
    <?php include 'tracker.php' ?>
    <?php include 'service_worker.php' ?>
    <script type="module" src="<?= static_url('src/js/main.js') ?>"></script>
  </body>
</html>
