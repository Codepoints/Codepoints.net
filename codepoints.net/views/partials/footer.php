    <footer class="page-footer">
      <p><?php printf(__('This is Codepoints.net, a site dedicated to all things characters, letters and Unicode. The site is run by %s. The content can be %s under the given terms. We’d like to thank %s for making Codepoints.net possible. %s are always welcome.'),
          '<a href="http://www.manuel-strehl.de">Manuel Strehl</a>',
          '<a rel="nofollow" href="'.q(url('about')).'#this_site">'.__('freely reused').'</a>',
          '<a rel="nofollow" href="'.q(url('about')).'#attribution">'.__('these fine people').'</a>',
          '<a rel="nofollow" href="https://github.com/Codepoints/codepoints.net">'.__('Feedback and contributions').'</a>')?></p>
      <nav>
        <ul>
          <li><a href="https://twitter.com/CodepointsNet"><?=_q('Twitter')?></a></li>
          <li><a href="https://blog.codepoints.net/"><?=_q('Blog')?></a></li>
          <li><a href="<?=q(url('about'))?>#this_site"><?=_q('About')?></a></li>
        </ul>
      </nav>
    </footer>
    <?php include 'tracker.php' ?>
  </body>
</html>
