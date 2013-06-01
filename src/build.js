{
  "appDir": "js/",
  "baseUrl": ".",
  "dir": "../static/js",
  "keepBuildDir": true,
  "skipDirOptimize": false,
  //"removeCombined": true,
  "paths": {
    "almond": "../vendor/almond/almond",
    "jquery": "../vendor/jquery/jquery",
    "jquery.ui": "../vendor/jquery.ui/dist/jquery-ui",
    "d3": "../vendor/d3/d3.v2",
    "webfont": "../vendor/webfontloader/target/webfont",
    "piwik": "http://piwik.manuel-strehl.de/piwik"
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
      "include": ["almond", "jquery.ui"],
      "exclude": ["http://piwik.manuel-strehl.de/piwik.js"]
    },
    {
      "name": "embedded",
      "include": ["almond"],
      "exclude": ["http://piwik.manuel-strehl.de/piwik.js"]
    },
    {
      "name": "dailycp",
      "exclude": ["jquery","jquery.ui","components/gettext"]
    },
    {
      "name": "glossary",
      "exclude": ["jquery","jquery.ui","components/gettext"]
    },
    {
      "name": "scripts",
      "exclude": ["jquery","jquery.ui","components/gettext"]
    },
    {
      "name": "searchform",
      "exclude": ["jquery","jquery.ui","components/gettext"]
    },
    {
      "name": "wizard",
      "exclude": ["jquery","jquery.ui","components/gettext"]
    }
  ]
}
