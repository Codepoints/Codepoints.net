require([
    'components/load_font'], function() {
  var scr = document.createElement('script');
  scr.src = 'http://piwik.manuel-strehl.de/piwik.js';
  scr.async = true;
  document.body.appendChild(scr);
});
