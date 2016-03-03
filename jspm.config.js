SystemJS.config({
  packageConfigPaths: [
    "npm:@*/*.json",
    "npm:*.json",
    "github:*/*.json"
  ],
  transpiler: "plugin-babel",

  map: {
    "jquery": "npm:jquery@2.2.1",
    "jquery-ui": "github:components/jqueryui@1.11.4",
    "plugin-babel": "npm:systemjs-plugin-babel@0.0.7",
    "process": "github:jspm/nodelibs-process@0.2.0-alpha",
    "webfontloader": "npm:webfontloader@1.6.22"
  },

  packages: {
    "Codepoints.net": {
      "main": "main.js",
      "format": "es6"
    },
    "github:components/jqueryui@1.11.4": {
      "map": {
        "jquery": "npm:jquery@2.2.1"
      }
    }
  }
});
