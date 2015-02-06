require([
    'components/load_font'], function() {
  var scr = document.createElement('script');
  scr.src = 'http://stats.codepoints.net/piwik.js';
  scr.async = true;
  document.body.appendChild(scr);
});
