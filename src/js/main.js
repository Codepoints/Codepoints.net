/**
 * I G N O R E   M E !
 * I'm only here for debugging codepoints.js.
 */

requirejs.config({
  "paths": {
    "almond": "../vendor/almond/almond",
    "jquery": "../vendor/jquery/jquery",
    "jquery.ui": "../vendor/jquery.ui/dist/jquery-ui",
    "d3": "../vendor/d3/d3.v2",
    "webfont": "../vendor/webfontloader/target/webfont"
  },
  "shim": {
    "webfont": {
      "exports": "WebFont"
    },
    "d3": {
      "exports": "d3"
    }
  }
});

require(['codepoints']);
