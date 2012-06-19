    </div>
    <div class="hidden" id="footer_search">
      <?php include "quicksearch.php"?>
    </div>
    <footer class="ft">
      <nav>
        <p class="tx">This is Codepoints.net, a site dedicated to all
        things Unicode and characters. The site is run by
        <a href="http://www.manuel-strehl.de">Manuel Strehl</a>.
        <a href="https://github.com/Boldewyn/codepoints.net">Feedback
        and contributions</a> are always welcome.</p>
        <ul>
          <li><a href="/">Start</a></li>
          <li><a href="http://blog.codepoints.net/">Blog</a></li>
          <li><a href="<?php e($router->getUrl('about'))?>#this_site">About</a></li>
        </ul>
      </nav>
    </footer>
    <script id="_ts" async="async">var _paq=_paq||[];(function(){var u="http://piwik.manuel-strehl.de/";_paq.push(['setSiteId',4]);_paq.push(['setTrackerUrl',u+'piwik.php']);_paq.push(['trackPageView']);_paq.push(['enableLinkTracking']);var d=document,g=d.createElement('script'),s=d.getElementsByTagName('script')[0];g.type='text/javascript';g.defer=true;g.async=true;g.src=u+'piwik.js';s.parentNode.insertBefore(g,s);})();</script>
    <script>WebFontConfig={google:{families:['Droid Serif:n,i,b,ib','Droid Sans:n,b']}};</script>
<?php if(CP_DEBUG):?>
    <script src="/static/js/jquery.js"></script>
    <script src="/static/js/jquery.ui.js"></script>
    <script src="/static/js/webfont.js"></script>
    <script src="/static/js/jquery.cachedajax.js"></script>
    <script src="/static/js/jquery.tooltip.js"></script>
    <script src="/static/js/jquery.glossary.js"></script>
    <script src="/static/js/codepoints.js"></script>
<?php else:?>
    <script src="/static/js/_.js"></script>
<?php endif?>
    <?php if (isset($footer_scripts)): foreach($footer_scripts as $sc):?>
        <script src="<?php e($sc)?>"></script>
    <?php endforeach; endif?>
  </body>
</html>
