SystemJS.config({
  baseURL: "/static/js/",
  paths: {
    "github:*": "jspm_packages/github/*",
    "npm:*": "jspm_packages/npm/*",
    "Codepoints.net/": "src/js/"
  }
});
