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
    document.title = data.filter('title').text();
    callback(data);
  });
}

var stage;

function animatePage($this, url, a1, a2) {
  var offset = "" + ($this.offset().left) + "px " + ($this.offset().top) + "px",
      tr = $('>*', stage).wrapAll('<div class="slide bottom"></div>')
            .closest('.slide').css('MozTransformOrigin', offset)
            .addClass(a1),
      tmp;
  getPage(url, function (data) {
    data = data.filter('.stage').contents();
    tmp = $('<div class="slide top '+a2+'"></div>').html(data)
               .appendTo(stage).css('MozTransformOrigin', offset);
    window.setTimeout(function() {
      tr.remove();
      tmp.replaceWith(tmp.contents());
    }, 3000);
  });
  return false;
}

$.fn.tooltip = function() {
  $('[title]', this).each(function() {
    var $this = $(this),
        title = $this.attr('title'),
        tip = $('<p class="tooltip"></p>').text(title);
    $this.removeAttr('title');
    tip.hide().appendTo('body');
    $this.on('mouseenter', function() {
      tip.fadeIn('slow').position({
        my: 'top',
        at: 'bottom',
        of: $this,
        offset: '0 8px',
        collision: 'fit'
      });
    }).on('mouseleave', function() {
      tip.hide();
    });
  });
  return this;
};

$(function() {
  stage = $('.stage');
  stage.on('click', 'a.cp', function() {
    return animatePage($(this), this.href, 'zoomin', 'maximize');
  });
  stage.on('click', 'a.bl, a.pl', function() {
    return animatePage($(this), this.href, 'minimize', 'zoomout');
  });
  $(document).on('keydown', function(e) {
    console.log(e.which);
  }).tooltip();
});

})(this, jQuery);
