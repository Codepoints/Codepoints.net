/**
 * cache AJAX requests locally
 */
define(['jquery'], function($) {

  var Cache = {};

  $.cachedAjax = function(url, options) {
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
      r = $.get(url, options).done(function(data) {
        Cache[url] = data;
      });
    }
    return r;
  };

});
