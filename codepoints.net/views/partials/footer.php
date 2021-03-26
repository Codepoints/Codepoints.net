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
          <li><a href="<?=q(url('about'))?>#this_site">
            <svg width="16" height="16"><use xlink:href="/api/v1/glyph/1F6C8#U1F6C8"/></svg>
            <?=_q('About')?></a></li>
        </ul>
      </nav>
    </footer>
    <?php include 'tracker.php' ?>
    <?php include 'service_worker.php' ?>
  </body>
</html>
