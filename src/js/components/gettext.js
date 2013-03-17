define(function() {
  var gettext = {};

  gettext.gettext = function(s) {
    var args = Array.prototype.slice.call(arguments, 1),
        item;
    /* do lookup here... */
    while (args.length) {
      item = args.shift();
      s = s.replace(/%s/, item);
    }
    return s;
  };

  return gettext;
});
