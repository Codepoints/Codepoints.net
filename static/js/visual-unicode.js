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

function createMask(element) {
  var slide = $('<div class="slide middle"/>').css({
    top: 0,
    left: 0,
    right: 0,
    bottom: 0
  }).on('click', function(){
    $(this).fadeOut(function() { $(this).remove(); });
  }),
      top = element.offset().top,
      left = element.offset().left,
      width = element.outerWidth(),
      height = element.outerHeight(),
      border = element.css('borderLeftWidth') + ' ' +
               element.css('borderLeftStyle') + ' ' +
               element.css('borderLeftColor'),
      bottom = $(document).height() - top - height,
      base = {background: 'white',
        position: 'absolute' };
  slide.append($('<div/>').css($.extend({
    top: 0,
    left: 0,
    width: left,
    height: top
  }, base))).append($('<div/>').css($.extend({
    top: 0,
    left: left,
    width: width,
    height: top,
    borderBottom: border
  }, base))).append($('<div/>').css($.extend({
    top: 0,
    left: left + width,
    right: 0,
    height: top
  }, base))).append($('<div/>').css($.extend({
    top: top,
    left: 0,
    width: left,
    height: height,
    borderRight: border
  }, base))).append($('<div/>').css($.extend({
    top: top,
    left: left+width,
    right: 0,
    height: height,
    borderLeft: border
  }, base))).append($('<div/>').css($.extend({
    top: top+height,
    left: 0,
    width: left,
    height: bottom
  }, base))).append($('<div/>').css($.extend({
    top: top+height,
    left: left,
    width: width,
    height: bottom,
    borderTop: border
  }, base))).append($('<div/>').css($.extend({
    top: top+height,
    left: left + width,
    right: 0,
    height: bottom
  }, base))).css({
    MozTransformOrigin: (left + width/2) + 'px ' +
                        (top + height/2) + 'px'
  });
  return slide;
}

function getPage(url, callback) {
  if (! $.isFunction(callback)) {
    callback = function(data) {
      $('.stage').empty().append(data.find('.stage').contents());
    };
  }
  var action = $.get;
  if (url in Cache) {
    action = function (url, func) {
      func(Cache[url]);
    };
  }
  action(url, function(data) {
    history.pushState({}, '', url);
    data = $(data);
    document.title = data.find('title').text();
    callback(data);
  });
}

$(function() {
  var stage = $('.stage');
  stage.on('click', 'a.cp', function() {
    var $this = $(this),
        url = this.href;
    getPage(url, function (data) {
      var tr = $('>*', stage).wrapAll('<div class="slide top"></div>')
                .closest('.slide');
      data = data.filter('.stage').contents();
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
    });
    return false;
  });
  stage.on('click', 'a.bl, a.pl', function() {
    var $this = $(this),
        url = this.href;
    getPage(url, function (data) {
      var tr = $('>*', stage).wrapAll('<div class="slide bottom"></div>')
                .closest('.slide');
      data = data.filter('.stage').contents();
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
    });
    return false;
  });
  $(document).on('keydown', function(e) {
    console.log(e.which);
  });
});

})(this, jQuery);
