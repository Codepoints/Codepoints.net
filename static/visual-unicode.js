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
      var tr = $('body>*').wrapAll('<div class="slide middle"></div>')
                .closest('div');
      data = $(data.replace(/[\s\S]*<body\b[^>]*>([\s\S]*)<\/body>[\s\S]*/,
                            '$1'))
      var tmp = $('<div class="slide bottom maximize"></div>').html(data)
                 .appendTo('body');
      tr.add(tmp).css('MozTransformOrigin',
        "" + ($this.offset().left) + "px " +
        "" + ($this.offset().top) + "px");
      tr.addClass('zoomin');
      window.setTimeout(function() {
        tr.remove();
        tmp.replaceWith(tmp.contents());
      }, 3000);
    });
    return false;
  });
  $('body').on('click', 'a.bl', function() {
    var $this = $(this),
        url = this.href;
    $.get(url, function(data) {
      history.pushState({}, '', url);
      document.title = /<title>([\s\S]*)<\/title>/.exec(data)[1];
      var tr = $('body>*').wrapAll('<div class="slide middle"></div>')
                .closest('div');
      data = $(data.replace(/[\s\S]*<body\b[^>]*>([\s\S]*)<\/body>[\s\S]*/,
                            '$1'))
      var tmp = $('<div class="slide top zoomout"></div>').html(data)
                 .appendTo('body');
      tr.add(tmp).css('MozTransformOrigin',
        "" + ($this.offset().left) + "px " +
        "" + ($this.offset().top) + "px");
      tr.addClass('minimize');
      window.setTimeout(function() {
        tr.remove();
        tmp.replaceWith(tmp.contents());
      }, 3000);
    });
    return false;
  });
});

})(this, jQuery);
