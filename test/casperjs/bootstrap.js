
var home = 'http://codepoints.localhost/';
var width = 800;
var height = 800;

function _take_screenshot(type) {
  var path = require('system').env.PWD + '/screenshots/' +
              type + Date.now() + '.png';
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
