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
    "d3": "../vendor/d3/d3",
    "d3.geo": "../vendor/d3/d3.geo",
    "webfont": "../vendor/webfontloader/target/webfont"
  },
  "shim": {
    "webfont": {
      "exports": "webfont"
    }
  },
  "modules": [
    {
      "name": "codepoints",
      "include": ["almond", "jquery.ui"]
    },
    {
      "name": "embedded"
    },
    {
      "name": "dailycp",
      "exclude": ["jquery","jquery.ui"]
    },
    {
      "name": "glossary",
      "exclude": ["jquery","jquery.ui"]
    },
    {
      "name": "scripts",
      "exclude": ["jquery","jquery.ui"]
    },
    {
      "name": "searchform",
      "exclude": ["jquery","jquery.ui"]
    },
    {
      "name": "wizard",
      "exclude": ["jquery","jquery.ui"]
    }
  ]
}
