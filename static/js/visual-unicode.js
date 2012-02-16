(function(window, $, undefined){

var Config = {
  path: '/projekte/visual-unicode/'
};

var Cache = {};

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
  var stage = $('.stage');
  stage.on('click', 'a.cp', function() {
    var $this = $(this),
        url = this.href;
    if (url in Cache) {
      showCP(url, Cache[url]);
    } else {
      $.get(url, function(data) {
        Cache[url] = data;
        showCP(url, data);
      });
    }
    function showCP(url, data) {
      history.pushState({}, '', url);
      document.title = /<title>([\s\S]*)<\/title>/.exec(data)[1];
      var tr = $('>*', stage).wrapAll('<div class="slide middle"></div>')
                .closest('.slide');
      data = $(data.replace(/[\s\S]*<body\b[^>]*>([\s\S]*)<\/body>[\s\S]*/,
                            '$1'))
      var tmp = $('<div class="slide bottom maximize"></div>').html(data)
                 .appendTo(stage);
      tr.add(tmp).css('MozTransformOrigin',
        "" + ($this.offset().left) + "px " +
        "" + ($this.offset().top) + "px");
      tr.addClass('zoomin');
      window.setTimeout(function() {
        tr.remove();
        tmp.replaceWith(tmp.contents());
      }, 3000);
    }
    return false;
  });
  stage.on('click', 'a.bl', function() {
    var $this = $(this),
        url = this.href;
    if (url in Cache) {
      showBlock(url, Cache[url]);
    } else {
      $.get(url, function(data) {
        Cache[url] = data;
        showBlock(url, data);
      });
    }
    function showBlock(url, data) {
      history.pushState({}, '', url);
      document.title = /<title>([\s\S]*)<\/title>/.exec(data)[1];
      var tr = $('>*', stage).wrapAll('<div class="slide middle"></div>')
                .closest('.slide');
      data = $(data.replace(/[\s\S]*<body\b[^>]*>([\s\S]*)<\/body>[\s\S]*/,
                            '$1'))
      var tmp = $('<div class="slide top zoomout"></div>').html(data)
                 .appendTo(stage);
      tr.add(tmp).css('MozTransformOrigin',
        "" + ($this.offset().left) + "px " +
        "" + ($this.offset().top) + "px");
      tr.addClass('minimize');
      window.setTimeout(function() {
        tr.remove();
        tmp.replaceWith(tmp.contents());
      }, 3000);
    }
    return false;
  });
});

})(this, jQuery);
