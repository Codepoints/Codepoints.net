      </div>
    </div>
    <footer class="page-footer">
      <p><?php printf(__('This is Codepoints.net, a site dedicated to all things characters, letters and Unicode. The site is run by %s. The content can be %s under the given terms. We’d like to thank %s for making Codepoints.net possible. %s are always welcome.'),
          '<a href="http://www.manuel-strehl.de">Manuel Strehl</a>',
          '<a rel="nofollow" href="'.q(url('about')).'#this_site">'.__('freely reused').'</a>',
          '<a rel="nofollow" href="'.q(url('about')).'#attribution">'.__('these fine people').'</a>',
          '<a rel="nofollow" href="https://github.com/Codepoints/codepoints.net">'.__('Feedback and contributions').'</a>')?></p>
      <nav>
        <ul>
          <li><a href="https://twitter.com/CodepointsNet">
            <svg width="16" height="16"><use xlink:href="/api/v1/glyph/1F426#U1F426"/></svg>
            <?=_q('Twitter')?></a></li>
          <li><a href="https://blog.codepoints.net/">
            <svg width="16" height="16"><use xlink:href="/api/v1/glyph/1F56E#U1F56E"/></svg>
            <?=_q('Blog')?></a></li>
          <li class="search"><a href="<?=q(url('search'))?>" rel="search">
            <svg width="16" height="16"><use xlink:href="/api/v1/glyph/1F50E#U1F50E"/></svg>
            <?=_q('Search')?></a></li>
          <li class="scripts"><a href="<?=q(url('scripts'))?>">
            <svg width="16" height="16"><use xlink:href="/api/v1/glyph/1F310#U1F310"/></svg>
            <?=_q('Scripts')?></a></li>
          <li class="random"><a rel="nofollow" href="<?=q(url('random'))?>">
            <svg width="16" height="16"><use xlink:href="/api/v1/glyph/27F3#U27F3"/></svg>
            <?=_q('Random')?></a></li>
          <li class="about"><a href="<?=q(url('about'))?>#this_site">
            <svg width="16" height="16"><use xlink:href="/api/v1/glyph/1F6C8#U1F6C8"/></svg>
            <?=_q('About')?></a></li>
        </ul>
      </nav>
      <?php include 'form-choose-language.php' ?>
      <?php include 'form-quicksearch.php' ?>
    </footer>
    <?php include 'tracker.php' ?>
    <?php include 'service_worker.php' ?>
    <script type="module" src="<?= static_url('src/js/main.js') ?>"></script>
  </body>
</html>
