(function(window, $, undefined){

var Cache = {};

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

/**
 * replace the title attribute with a custom tooltip
 */
$.fn.tooltip = function() {
  $('[title]', this).each(function() {
    var origin = $(this),
        title = origin.attr('title'),
        arrow = $('<i></i>'),
        tip = $('<p class="tooltip"></p>').text(title)
                .prepend(arrow)
                .hide().appendTo('body').on('mouseenter', function() {
                  $(this).hide(); });
    origin.removeAttr('title')
          .on('mouseenter', function() {
      tip.stop(true, true).fadeIn('slow').position({
        my: 'top',
        at: 'bottom',
        of: origin,
        offset: '0 8px',
        collision: 'fit flip',
        using: function(pos) {
          var el = $(this);
          el.css({
            left: pos.left,
            top: pos.top
          });
          if (pos.left === 0) {
            arrow.css({ left: '' + (origin.offset().left +
              (origin.outerWidth()/2)) + 'px' });
          } else if(pos.left > $(window).width() - tip.outerWidth() - 10) {
            arrow.css({ left: '' + (origin.offset().left +
              (origin.outerWidth()/2) - pos.left) + 'px' });
          } else {
            arrow.css({ left: '50%' });
          }
          if (pos.top < origin.offset().top) {
            arrow.addClass('down');
          } else {
            arrow.removeClass('down');
          }
        }
      });
    }).on('mouseleave click', function() {
      tip.stop(true, true).hide();
    });
  });
  return this;
};

$(function() {
  stage = $('.stage');
  //stage.on('click', 'a.cp', function() {
  //  return animatePage($(this), this.href, 'zoomin', 'maximize');
  //});
  //stage.on('click', 'a.bl, a.pl', function() {
  //  return animatePage($(this), this.href, 'minimize', 'zoomout');
  //});
  $(document).tooltip();
  $('nav a[rel="search"]').on('click', function() {
    var $this = $(this),
        el = $('#footer_search').css({
          right: $(window).width() - $this.offset().left - $this.outerWidth(),
          top: $this.offset().top + $this.outerHeight() + 4
        });
    if (! el.data('extended')) {
      el.data('extended',
        true).append($('<p></p>').append($('<a></a>').attr('href',
              $this.attr('href')).text('Extended Search')));
    }
    if (el.is(':hidden')) {
      el.slideDown('normal').find(':text:eq(0)').focus();
      $(document).one('click keydown', function __hideMe(e) {
        if (el.find(e.target).length === 0 &&
            (e.type === 'click' || e.which === 27)) {
          el.slideUp('normal');
        } else {
          $(document).one('click keydown', __hideMe);
        }
      });
    }
    return false;
  });
});

})(this, jQuery);
