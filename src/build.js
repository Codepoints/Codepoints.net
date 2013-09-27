{
  "appDir": "js/",
  "baseUrl": ".",
  "dir": "../codepoints.net/static/js",
  "keepBuildDir": false,
  "skipDirOptimize": false,
  //"removeCombined": true,
  "paths": {
    "almond": "../vendor/almond/almond",
    "jquery": "../vendor/jquery/jquery",
    "jqueryui": "../vendor/jquery.ui/jqueryui",
    "d3": "../vendor/d3/d3.v2",
    "webfont": "../vendor/webfontloader/target/webfont",
    "zeroclipboard": "../vendor/zeroclipboard/ZeroClipboard"
  },
  "shim": {
    "webfont": {
      "exports": "WebFont"
    },
    "d3": {
      "exports": "d3"
    },
    "zeroclipboard": {
      "exports": "ZeroClipboard"
    }
  },
  "modules": [
    {
      "name": "codepoints",
      "include": ["almond"],
    },
    {
      "name": "embedded",
      "include": ["almond"],
    },
    {
      "name": "dailycp",
      "exclude": ["jquery","jqueryui/core","jqueryui/widget","components/gettext"]
    },
    {
      "name": "glossary",
      "exclude": ["jquery","components/gettext"]
    },
    {
      "name": "scripts",
      "exclude": ["jquery","jqueryui/core","jqueryui/dialog","components/gettext"]
    },
    {
      "name": "searchform",
      "exclude": ["jquery","jqueryui/core","jqueryui/dialog","components/gettext","components/jquery.tooltip"]
    },
    {
      "name": "wizard",
      "exclude": ["jquery","components/gettext"]
    }
  ]
}
