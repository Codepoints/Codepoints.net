const stats_url = 'https://stats.codepoints.net/';
var _paq = window._paq = window._paq || [];

_paq.push(['disableCookies']);
_paq.push(['enableLinkTracking']);
_paq.push(['setTrackerUrl',stats_url+'matomo.php']);
_paq.push(['setSiteId','4']);

const script = document.createElement('script');
script.src = stats_url+'matomo.js';
document.head.appendChild(script);

export function track(item) {
  window._paq.push(item);
}

export function trackByContent(node) {
  track(['setDocumentTitle', document.title]);
  track(['setCustomUrl', window.location]);
  var trackingContainer = node.querySelector('script[type="application/tracker+json"]');
  if (trackingContainer) {
    try {
      track(JSON.parse(trackingContainer.textContent));
    } catch {
      track(['trackPageView', document.title]);
      track(['trackEvent', 'error', 'malformed_trackingdata']);
    }
  } else {
    track(['trackPageView', document.title]);
  }
}
