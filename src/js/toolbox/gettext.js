'use strict';


var global_gettext,
    lang = document.documentElement.getAttribute('lang');

if ('gettext' in window) {
  global_gettext = window.gettext;
} else {
  global_gettext = {catalog:{}};
}

/**
 * translate a string by catalog lookup
 *
 * Occurances of "%s" are replaced by additional
 * parameters.
 */
export default function _(s) {
  var args = Array.prototype.slice.call(arguments, 1),
      item;

  /* do lookup in catalog */
  if (lang in global_gettext.catalog && s in global_gettext.catalog[lang]) {
    s = global_gettext.catalog[lang][s];
  }

  while (args.length) {
    item = args.shift();
    s = s.replace(/%s/, item);
  }

  return s;
}
