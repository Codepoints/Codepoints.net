{
  "appDir": "js/",
  "baseUrl": ".",
  "dir": "../codepoints.net/static/js",
  "keepBuildDir": true,
  "skipDirOptimize": false,
  //"removeCombined": true,
  "paths": {
    "almond": "../vendor/almond/almond",
    "jquery": "../vendor/jquery/jquery",
    "jqueryui": "../vendor/jquery.ui/jqueryui",
    "d3": "../vendor/d3/d3.v2",
    "webfont": "../vendor/webfontloader/target/webfont",
    "piwik": "http://piwik.manuel-strehl.de/piwik.js"
  },
  "shim": {
    "webfont": {
      "exports": "WebFont"
    },
    "d3": {
      "exports": "d3"
    }
  },
  "modules": [
    {
      "name": "codepoints",
      "include": ["almond"],
      "exclude": ["http://piwik.manuel-strehl.de/piwik.js"]
    },
    {
      "name": "embedded",
      "include": ["almond"],
      "exclude": ["http://piwik.manuel-strehl.de/piwik.js"]
    },
    {
      "name": "dailycp",
      "exclude": ["jquery","components/gettext"]
    },
    {
      "name": "glossary",
      "exclude": ["jquery","components/gettext"]
    },
    {
      "name": "scripts",
      "exclude": ["jquery","components/gettext"]
    },
    {
      "name": "searchform",
      "exclude": ["jquery","components/gettext"]
    },
    {
      "name": "wizard",
      "exclude": ["jquery","components/gettext"]
    }
  ]
}
