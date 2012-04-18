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
  var targets = $();
  if (this.is(function() {
    return this.nodeType === 1 && this.title;
  })) {
    targets = targets.add(this);
  }
  targets.add('[title]', this).each(function() {
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
  /**
  * Determine the scrolling element
  *
  * @see http://stackoverflow.com/questions/2837178
  */
  var scrollElement = (function (tags) {
    var el, $el;
    while (el = tags.pop()) {
      $el = $(el);
      if ($el.scrollTop() > 0){
        return $el;
      } else if($el.scrollTop(1).scrollTop() > 0) {
        return $el.scrollTop(0);
      }
    }
    return $();
  })(["html", "body"]);

  stage = $('.stage');
  //stage.on('click', 'a.cp', function() {
  //  return animatePage($(this), this.href, 'zoomin', 'maximize');
  //});
  //stage.on('click', 'a.bl, a.pl', function() {
  //  return animatePage($(this), this.href, 'minimize', 'zoomout');
  //});

  /** init tooltips */
  $(document).tooltip();

  /* scale the front headline text */
  var headline = $('.front h1'), resizer;
  if (headline.length) {
    // with hat tip to fittext.js
    resizer = function () {
      headline.css('font-size', Math.max(Math.min(headline.width() / 7.5, 160), 20));
    };
    resizer();
    $(window).on("load resize", resizer);
  }

  /** let in-page links scroll smooth */
  $(document).on('click tap', 'nav a[href^="#"], a[rel~="internal"]', function() {
    var a = $(this), t = a.attr("href");
    if (t.length > 1 && $(t).length) {
      scrollElement.animate({scrollTop: $(t).offset().top - 10}, 1000);
      return false;
    }
  });

  /* display search form */
  $('nav a[rel="search"]').on('click', function() {
    var $this = $(this),
        el = $('#footer_search').show().position({
          my: 'left top',
          at: 'left bottom',
          of: $this,
          collision: 'fit'
        }).hide();
    if (! el.data('extended')) {
      el.data('extended',
        true).append($('<p></p>').append($('<a></a>').attr('href',
              $this.attr('href')).text('Extended Search')));
    }
    if (el.is(':hidden')) {
      el.slideDown('normal').find(':text:eq(0)').focus();
      $(document).one('tap click keydown', function __hideMe(e) {
        if (e.which === 27 || (el.find(e.target).length === 0 &&
            $.inArray(e.type, ['tap', 'click']) > -1)) {
          el.slideUp('normal');
        } else {
          $(document).one('tap click keydown', __hideMe);
        }
      });
    }
    return false;
  });

  /* keyboard navigation */
  $(document).on('keydown', function(e) {
    if (e.target !== document.body) {
      return;
    }
    if (e.shiftKey && ! e.metaKey && ! e.ctrlKey && ! e.altKey) {
      var a = [], click = true;
      switch (e.which) {
        case 33: // PgDn: paginate back
          a = $('.pagination .prev a:eq(0)');
          break;
        case 34: // PgUp: paginate forth
          a = $('.pagination .next a:eq(0)');
          break;
        case 36: // Pos1: homepage
          a = $('a[rel="start"]:eq(0)');
          break;
        case 37: // ArrL: previous element
          a = $('a[rel="prev"]:eq(0)');
          break;
        case 38: // ArrU: containing block
          a = $('a[rel="up"]:eq(0)');
          break;
        case 39: // ArrR: next element
          a = $('a[rel="next"]:eq(0)');
          break;
        case 40: // ArrD: first child
          a = $('.data a:eq(0)');
          click = false;
          break;
        case 83: // S: search
          a = $('a[rel="search"]:eq(0)');
          break;
        case 65: // A: about
          a = $('nav .about a:eq(0)');
          break;
      }
      if (a.length) {
        a.trigger('focus');
        if (click) {
          window.location.href = a[0].href;
        }
        return false;
      }
    }
  });

  /* search form enhancement */
  $('.extended.searchform').each(function() {
    var $form = $(this), fields = $('.propsearch, .boolsearch', $form).hide(),
        submitset = $('.submitset', $form),
        addlist = $('<ul class="query-add ui-widget ui-widget-content ui-corner-all"></ul>').insertBefore(submitset),
        addfields = $(),
        add = $('<p><button type="button" title="add new query">+</button></p>')
                .insertBefore(submitset).tooltip().find('button'),
        values = {},
        menu = $('<ul class="ui-menu ui-widget ui-widget-content"></ul>');

    fields.filter('.propsearch').each(function() {
      var field = $(this), val = [],
          legend = $('legend', field).text().replace(/:\s*$/, '');
      $(':checkbox', field).each(function() {
        var chk = $(this), label = $('label[for="'+this.id+'"]', field).text();
        val.push([chk, label]);
        if (chk[0].checked) {
          _createItem(legend, label, chk);
        }
      });
      values[legend] = val;
    });
    values['Boolean Value'] = [];
    fields.filter('.boolsearch').each(function() {
      var sel = $('select', this), label = $('label', this).text();
      values['Boolean Value'].push([sel, label]);
      if (sel.find('option[value="1"]')[0].selected) {
        _createItem('Boolean Value', label, sel);
      }
    });
    $.each(values, function(k, v) {
      menu.append($('<li class="ui-menu-item"><a href="#">'+k+'</a></li>')
                  .data('v', v).data('k', k));
    });

    $(document).on('keypress click tap', function() {
      menu.slideUp();
    });

    menu.css({
      display: 'none',
      position: 'absolute'
    }).appendTo('body').on('click tap', 'li', function() {
      var li = $(this), v = li.data('v'), k = li.data('k'),
          dlg = $('<ul class="query-choose"></ul>');
      $.each(v, function(j, vv) {
        dlg.append($('<li><a class="button" href="#">'+vv[1]+'</a></li>')
          .on('click tap', 'a', function() {
            if (vv[0].is(':checkbox')) {
              vv[0][0].checked = true;
            } else {
              vv[0].find('option[value="1"]')[0].selected = true;
            }
            dlg.dialog('close').dialog('destroy').remove();
            _createItem(k, vv[1], vv[0]);
            return false;
          }));
      });
      dlg.dialog({
        title: k,
        modal: true,
        width: Math.min($(window).width(), 1030)
      });
      menu.slideUp();
      return false;
    });

    add.on('click', function() {
      menu.show().position({
        my: 'left top',
        at: 'left bottom',
        of: this,
        colision: 'fit flip'
      }).hide().slideDown();
      return false;
    });

    /** create a single search field item */
    function _createItem(key, value, input) {
      return $('<li class="query-item"></li>')
        .html(key+': '+value+'<button type="button">remove</button>')
          .find('button').button({
            text: false,
            icons: {primary: 'ui-icon-close', secondary: false}
          }).tooltip().click(function() {
            var i = $(this).closest('li');
            if (input.is(':checkbox')) {
              input[0].checked = false;
            } else {
              input.find('option[value=""]')[0].selected = true;
            }
            i.slideUp('fast', i.remove);
          }).end()
        .appendTo(addlist);
    }

  });

});

})(this, jQuery);
