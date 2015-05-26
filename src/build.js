{
  "appDir": "js/",
  "baseUrl": ".",
  "dir": "../codepoints.net/static/js",
  "keepBuildDir": false,
  "skipDirOptimize": false,
  //"removeCombined": true,
  "paths": {
    "almond": "../../node_modules/almond/almond",
    "jquery": "../../node_modules/jquery/dist/jquery",
    "jqueryui": "../../node_modules/jquery-ui/jqueryui",
    "d3": "../../node_modules/d3/d3.v2",
    "webfont": "../../node_modules/webfontloader/target/webfont",
    "zeroclipboard": "../../node_modules/zeroclipboard/dist/ZeroClipboard"
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
