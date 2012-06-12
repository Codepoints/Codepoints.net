/**
 * cache AJAX requests locally
 */
(function($) {

  var Cache = {};

  $.cachedAjax = function(url, options) {
    var r;
    if (url in Cache) {
      // a cached response exists
      r = $.when(Cache[url]);
      if ('success' in options) {
        // call the success callback, if one is given
        options.success(Cache[url]);
      } else if ($.isFunction(options)) {
        options(Cache[url]);
      }
    } else {
      r = $.get(url, options);
    }
    return r;
  };

})(jQuery);
