    </div>
    <div class="hidden" id="footer_search">
      <?php include "quicksearch.php"?>
    </div>
    <footer class="ft">
      <nav>
        <p class="tx"><?php printf(__('This is Codepoints.net, a site dedicated to all things characters, letters and Unicode. The site is run by %s. The content can be %s under the given terms. We’d like to thank %s for making Codepoints.net possible. %s are always welcome.'),
          '<a href="http://www.manuel-strehl.de">Manuel Strehl</a>',
          '<a rel="nofollow" href="'.q($router->getUrl('about')).'#this_site">'.__('freely reused').'</a>',
          '<a rel="nofollow" href="'.q($router->getUrl('about')).'#attribution">'.__('these fine people').'</a>',
          '<a rel="nofollow" href="https://github.com/Codepoints/codepoints.net">'.__('Feedback and contributions').'</a>')?></p>
        <?php if (! isset($_COOKIE) || ! isset($_COOKIE['_eu']) || $_COOKIE['_eu'] !== 'hide'):?>
        <p class="tx cookies"><?php printf(__('We use cookies to enhance your experience and to develop the site further. We never track you for monetary reasons. If you do not want this, you can either use the Do-Not-Track feature of your browser or %s.'),
            '<a rel="nofollow" href="'.q($router->getUrl('about')).'#this_site">'.__('opt out explicitly').'</a>')?></p>
        <?php endif?>
        <ul>
          <li><a href="/"><?php _e('Start')?></a></li>
          <li><a href="https://twitter.com/CodepointsNet"><i class="icon-twitter"></i> <?php _e('Twitter')?></a></li>
          <li><a href="http://blog.codepoints.net/"><i class="icon-book"></i> <?php _e('Blog')?></a></li>
          <li><a href="<?php e($router->getUrl('about'))?>#this_site"><i class="icon-info-sign"></i> <?php _e('About')?></a></li>
        </ul>
      </nav>
    </footer>
    <?php include "partials/tracker.php"; ?>
    <script>WebFontConfig={google:{families:['Droid Serif:n,i,b,ib','Droid Sans:n,b']}};</script>
    <?php if ($lang !== "en"):?>
      <script src="<?php echo url('/static/locale/'.$lang.'.js')?>"></script>
    <?php endif?>
    <script src="<?php echo url('/static/js/main.js')?>" async
      <?php if (isset($footer_scripts)): ?>
        id="additional-scripts" data-src='<?php e(json_encode($footer_scripts))?>'
      <?php endif?>></script>
    <script>if('serviceWorker' in navigator){navigator.serviceWorker.register('/sw.js',{scope:'/'})}</script>
  </body>
</html>
