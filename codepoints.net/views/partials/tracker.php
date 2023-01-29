<?php
/**
 * @var string $view
 * @var ?string $q
 * @var ?boolean $is_fulltext_result
 * @var ?\Codepoints\Unicode\SearchResult $search_result
 */
?>
<script>var _paq=window._paq=window._paq||[];
_paq.push(['disableCookies']);
<?php if ($view === 'search' && isset($q) && $q && isset($search_result)): ?>
_paq.push(['trackSiteSearch', <?=json_encode($q)?>, false, <?=(
  (isset($is_fulltext_result) && $is_fulltext_result)?
    $search_result->count() :
    0 /* make sure that we know in the analytics view, that this search term
       * wasn't found in the fulltext table. */
)?>]);
<?php else: ?>
_paq.push(['trackPageView']);
<?php endif ?>
_paq.push(['enableLinkTracking']);
(function(){
var u="https://stats.codepoints.net/";
_paq.push(['setTrackerUrl',u+'matomo.php']);
_paq.push(['setSiteId','4']);
var d=document,g=d.createElement('script'),s=d.getElementsByTagName('script')[0];
g.async=true;g.src=u+'matomo.js';s.parentNode.insertBefore(g,s);
})()</script>
