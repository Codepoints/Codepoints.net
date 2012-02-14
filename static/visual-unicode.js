(function(window, $, undefined){

var Config = {
  path: '/projekte/visual-unicode/'
};

var Codepoint = Backbone.Model.extend({
  toInt: function() {
    var i = parseInt(this.id, 16);
    return (isNaN(i)? -1 : i);
  },
  urlRoot: Config.path + 'U+'
});

var UnicodeRange = Backbone.Collection.extend({
});

var Block = UnicodeRange.extend({
});

$(function() {
  $('body').on('click', 'a.cp', function() {
    var $this = $(this),
        url = this.href;
    $.get(url, function(data) {
      history.pushState({}, '', url);
      document.title = /<title>([\s\S]*)<\/title>/.exec(data)[1];
      var tr = $('body>*').wrapAll('<div id="transition"></div>').closest('#transition');
      tr.css('MozTransformOrigin',
        "" + ($this.offset().left) + "px " +
        "" + ($this.offset().top) + "px")
        .addClass('active');
      data = $(data.replace(/[\s\S]*<body\b[^>]*>([\s\S]*)<\/body>[\s\S]*/, '$1'))
      var tmp = $('<div id="next"></div>').html(data).appendTo('body');
      tmp.fadeIn(3000, function() {
        tr.remove();
        tmp.replaceWith(tmp.contents());
      });
    });
    return false;
  });
  $('body').on('click', 'a.bl', function() {
    var $this = $(this),
        url = this.href;
    $.get(url, function(data) {
      history.pushState({}, '', url);
      document.title = /<title>([\s\S]*)<\/title>/.exec(data)[1];
      var tr = $('body>*').wrapAll('<div id="minimize"></div>').closest('#minimize');
      data = $(data.replace(/[\s\S]*<body\b[^>]*>([\s\S]*)<\/body>[\s\S]*/, '$1'))
      var tmp = $('<div id="transition"></div>').html(data).appendTo('body');
      tmp.addClass('inverse');
      window.setTimeout(function() {
        tr.remove();
        tmp.replaceWith(tmp.contents());
      }, 3000);
    });
    return false;
  });
});

})(this, jQuery);
