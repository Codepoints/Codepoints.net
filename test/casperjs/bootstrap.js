
var sys = require('system');
var home = sys.env.CASPER_HOME || 'http://codepoints.localhost/';
var width = parseInt(sys.env.CASPER_WIDTH, 10) || 1280;
var height = parseInt(sys.env.CASPER_HEIGHT, 10) || 900;

function _take_screenshot(type) {
  var path = sys.env.PWD + '/screenshots/' + type + Date.now() + '.png';
  casper.capture(path);
  casper.echo('Screenshot saved to '+path);
}

casper.test.on('fail', function() {
  _take_screenshot('testFail');
});

/* we need to cater for Casper beta3 having issues here:
 * https://github.com/n1k0/casperjs/issues/687
 */
if (casper.test.options.failFast) {
  var testFailFunction = casper.test.listeners('fail')[0];
  casper.test.removeListener('fail', testFailFunction);
  casper.test.on('fail', testFailFunction);
}

casper.options.viewportSize  = {width : width, height: height};
casper.options.onLoadError   = _take_screenshot.bind(null, 'loadError');
casper.options.onWaitTimeout = _take_screenshot.bind(null, 'waitTimeout');
casper.options.onStepTimeout = _take_screenshot.bind(null, 'stepTimeout');

casper.test.done();
