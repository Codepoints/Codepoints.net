'use strict';

/**
 * cache AJAX requests locally
 */
var Cache = {};

export default function cached_ajax(url, options) {
  var r;

  if (url in Cache) {
    // a cached response exists
    r = $.when(Cache[url]);
    if (options && 'success' in options) {
      // call the success callback, if one is given
      options.success(Cache[url]);
    } else if ($.isFunction(options)) {
      options(Cache[url]);
    }
  } else {
    r = $.get(url, options).done((data) => Cache[url] = data);
  }

  return r;
}
