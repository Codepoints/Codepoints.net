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
    <script id="_ts"><?php
?>var _paq=_paq||[];<?php
?>(function(){<?php
  ?>var u="http://piwik.manuel-strehl.de/";<?php
  ?>_paq.push(['setSiteId',4]);<?php
  ?>_paq.push(['setTrackerUrl',u+'piwik.php']);<?php
  ?>_paq.push(['trackPageView']);<?php
  ?>_paq.push(['enableLinkTracking']);<?php
  ?>var d=document,<?php
      ?>g=d.createElement('script'),<?php
      ?>s=d.getElementsByTagName('script')[0];<?php
  ?>g.type='text/javascript';<?php
  ?>g.defer=true;<?php
  ?>g.async=true;<?php
  ?>g.src=u+'piwik.js';<?php
  ?>s.parentNode.insertBefore(g,s);<?php
?>})();</script>
    <script>WebFontConfig={google:{families:['Droid Serif:n,i,b,ib','Droid Sans:n,b']}};</script>
    <?php if ($lang !== "en"):?>
      <script src="/static/locale/<?php e($lang)?>.js!<?php e(CACHE_BUST)?>"></script>
    <?php endif?>
<?php if(CP_DEBUG):?>
    <script src="/src/vendor/requirejs/require.js"></script>
    <script>
require.config({
  "baseUrl": "/src/js/",
  "urlArgs": "bust=" +  (new Date()).getTime(),
  "paths": {
    "almond": "/src/vendor/almond/almond",
    "jquery": "/src/vendor/jquery/jquery",
    "jquery.ui": "/src/vendor/jquery.ui/dist/jquery-ui",
    "d3": "/src/vendor/d3/d3.v2",
    "webfont": "/src/vendor/webfontloader/target/webfont"
  },
  "shim": {
    "jquery": {
      "exports": "jQuery"
    },
    "jquery.ui": {
      "deps": ["jquery"]
    },
    "webfont": {
      "exports": "WebFont"
    },
    "d3": {
      "exports": "d3"
    }
  }
});

require(['codepoints']);
    </script>
<?php else:?>
    <script src="/static/js/codepoints.js!<?php e(CACHE_BUST)?>"></script>
<?php endif?>
    <?php if (isset($footer_scripts)): foreach($footer_scripts as $sc):?>
        <script src="<?php e($sc)?>!<?php e(CACHE_BUST)?>"></script>
    <?php endforeach; endif?>
  </body>
</html>
