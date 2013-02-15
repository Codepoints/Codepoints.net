    </div>
    <div class="hidden" id="footer_search">
      <?php include "quicksearch.php"?>
    </div>
    <footer class="ft">
      <nav>
        <p class="tx"><?php printf(__('This is Codepoints.net, a site dedicated to all things characters, letters and Unicode. The site is run by %s. The content can be %s under the given terms. We’d like to thank %s for making Codepoints.net possible. %s are always welcome.'),
          '<a href="http://www.manuel-strehl.de">Manuel Strehl</a>',
          '<a href="'.q($router->getUrl('about')).'#this_site">'.__('freely reused').'</a>',
          '<a href="'.q($router->getUrl('about')).'#attribution">'.__('these fine people').'</a>',
          '<a href="https://github.com/Boldewyn/codepoints.net">'.__('Feedback and contributions').'</a>')?></p>
        <?php if (! isset($_COOKIE) || ! isset($_COOKIE['_eu']) || $_COOKIE['_eu'] !== 'hide'):?>
        <p class="tx cookies"><?php printf(__('We use cookies to enhance your experience and to develop the site further. We never track you for monetary reasons. If you do not want this, you can either use the Do-Not-Track feature of your browser or %s.'),
            '<a href="'.q($router->getUrl('about')).'#this_site">'.__('opt out explicitly').'</a>')?></p>
        <?php endif?>
        <ul>
          <li><a href="/"><?php _e('Start')?></a></li>
          <li><a href="https://twitter.com/CodepointsNet"><i class="icon-twitter"></i> <?php _e('Twitter')?></a></li>
          <li><a href="http://blog.codepoints.net/"><i class="icon-book"></i> <?php _e('Blog')?></a></li>
          <li><a href="<?php e($router->getUrl('about'))?>#this_site"><i class="icon-info-sign"></i> <?php _e('About')?></a></li>
        </ul>
      </nav>
    </footer>
    <script id="_ts">var _paq=_paq||[];(function(){var u="http://piwik.manuel-strehl.de/";_paq.push(['setSiteId',4]);_paq.push(['setTrackerUrl',u+'piwik.php']);_paq.push(['trackPageView']);_paq.push(['enableLinkTracking']);var d=document,g=d.createElement('script'),s=d.getElementsByTagName('script')[0];g.type='text/javascript';g.defer=true;g.async=true;g.src=u+'piwik.js';s.parentNode.insertBefore(g,s);})();</script>
    <script>WebFontConfig={google:{families:['Droid Serif:n,i,b,ib','Droid Sans:n,b']}};</script>
<?php if(CP_DEBUG):?>
    <script src="/src/js_embed/jquery.js"></script>
    <script src="/src/js_embed/jquery.ui.js"></script>
    <script src="/src/js_embed/webfont.js"></script>
    <script src="/src/js_embed/jquery.cachedajax.js"></script>
    <script src="/src/js_embed/jquery.tooltip.js"></script>
    <script src="/src/js_embed/jquery.glossary.js"></script>
    <script src="/src/js_embed/codepoints.js"></script>
<?php else:?>
    <script src="/static/js/_.js!<?php e(CACHE_BUST)?>"></script>
<?php endif?>
    <?php if (isset($footer_scripts)): foreach($footer_scripts as $sc):?>
        <script src="<?php e($sc)?>!<?php e(CACHE_BUST)?>"></script>
    <?php endforeach; endif?>
  </body>
</html>
