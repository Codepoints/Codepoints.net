(function(window, $, undefined){

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

  /** init tooltips */
  $(document).tooltip().glossary();

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
      scrollElement.animate({scrollTop: $(t).offset().top - 20}, 1000);
      return false;
    }
  });

  /* display "to top" anchor under circumstances */
  $(window).on("load", function() {
    if ($(window).height() + 50 < $(document).height()) {
      $('footer.ft nav ul:eq(0)').prepend(
        $('<li><a href="#top">\u2B06 top</a></li>').find('a')
          .on('click', function() {
            scrollElement.animate({scrollTop: 0}, 500);
            return false;
          })
        .end());
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
              $this.attr('href')).text('Extended Search')))
             .append($('<p></p>').append($('<a></a>').attr('href',
              '/wizard').text('Find My Codepoint')));
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
        add = $('<p><button type="button">+ add new query</button></p>')
                .insertBefore(submitset).find('button'),
        search_values = {},
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
      search_values[legend] = val;
    });
    search_values['Other Property'] = [];
    fields.filter('.boolsearch').each(function() {
      var sel = $('select', this), label = $('label', this).text();
      search_values['Other Property'].push([sel, label]);
      if (sel.find('option[value="1"]')[0].selected) {
        _createItem('Other Property', label, sel);
      }
    });
    $.each(search_values, function(k, v) {
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
      // user selects a category from the menu
      var li = $(this);
      _showPropertyChooser(li.data('k'), li.data('v'),
        function(property, value, input) {
          if (input.is(':checkbox')) {
            input[0].checked = true;
          } else {
            input.find('option[value="1"]')[0].selected = true;
          }
          _createItem(property, value, input);
        });
      menu.slideUp();
      return false;
    });

    add.on('click tap', function() {
      menu.show().position({
        my: 'left top',
        at: 'left bottom',
        of: this,
        colision: 'fit flip'
      }).hide().slideDown();
      return false;
    });
    addlist.on('click tap', function(e) {
      if (e.target === this) {
        add.trigger('click');
        return false;
      }
    });

    /** show an option dialog for a single prop */
    function _showPropertyChooser(property, values, callback) {
      var dlg = $('<ul class="query-choose"></ul>');
      $.each(values, function(i, value) {
        dlg.append($('<li><a class="button" href="#">'+value[1]+'</a></li>')
          .on('click tap', 'a', function() {
            // user selects an option from this category
            callback(property, value[1], value[0]);
            dlg.dialog('close').dialog('destroy').remove();
            dlg = null;
            return false;
          }));
      });
      dlg.dialog({
        title: property,
        modal: true,
        width: Math.min($(window).width(), 1030)
      });
    }

    /** create a single search field item */
    function _createItem(property, value, input) {
      var valclass = '', valclick = jQuery.noop;
      if (input.is('select')) {
        valclass = ' y';
        valclick = function() {
          // switch value
          input.find('option[value=""]')[0].selected = false;
          if (input.find('option[value="1"]')[0].selected) {
            input.find('option[value="1"]')[0].selected = false;
            input.find('option[value="0"]')[0].selected = true;
            $(this).find('.value').addClass('n').removeClass('y');
          } else {
            input.find('option[value="0"]')[0].selected = false;
            input.find('option[value="1"]')[0].selected = true;
            $(this).find('.value').addClass('y').removeClass('n');
          }
          return false;
        };
      } else {
        valclick = function() {
          var that = $(this),
              values = search_values[property];
          _showPropertyChooser(property, values, function(property, value,
                                                          input2) {
            that.find('.value').text(value);
            input[0].checked = false;
            input2[0].checked = true;
          });
        };
      }
      return $('<li class="query-item"></li>')
        .html('<span class="key">'+property+':</span> <span class="value' +
              valclass + '" title="click to change">' +
              value + '</span> <button type="button">remove</button>')
          .find('button').button({
            text: false,
            icons: {primary: 'ui-icon-close', secondary: false}
          }).on('click tab', function() {
            // remove this field again
            var i = $(this).closest('li');
            if (input.is(':checkbox')) {
              input[0].checked = false;
            } else {
              input.find('option[value=""]')[0].selected = true;
            }
            i.slideUp('fast', i.remove);
            return false;
          }).end()
        .on('click tab', valclick).tooltip()
        .appendTo(addlist);
    }

  });

  var about = $('.payload.about');

  if (about.length) {
    var sect = about.find('>section'), n = 0, sel = [];
    if (sect.filter(window.location.hash).length) {
      n = sect.filter(window.location.hash).index();
      window.scrollTo(0,0);
    }
    sect.filter(':not(:eq('+n+'))').hide();
    sect.each(function() {
      var $this = $(this), $next = $this.next('section');
      sel.push('#' + this.id);
      if ($next.length) {
        $this.append('<p><a href="#'+$next[0].id+'"><i>Read on:</i> “'+$next.find('h1').text()+'”</a></p>');
      }
    });
    $(document).on('click tap', 'a[href^="#"]', function() {
      var t, h = this.hash;
      if ($.inArray(h, sel) > -1) {
        t = $(h);
        if (t.filter(':hidden').length) {
          sect.not(t).slideUp();
          window.location.hash = h;
          scrollElement.animate({scrollTop:0}, 300);
          t.slideDown();
        }
        return false;
      }
    });
  }

  /**
   * load a font to render the codepoint figure
   */
  var cp_fig = $('.codepoint figure .fig');
  if (cp_fig.length) {
    if (cp_fig.data('fonts')) {
      var cp_fonts = cp_fig.data('fonts').split(',');
      if (cp_fonts[0]) {
        window.WebFont.load({
          custom: {
            families: [cp_fonts[0]],
            urls: ['/static/fonts/'+encodeURIComponent(cp_fonts[0])+'.css']
          },
          active: function() {
            cp_fig.css({
              fontFamily: '"'+cp_fonts[0]+'", serif'
            });
            var _aside = cp_fig.closest('figure').next('aside');
            if (_aside.length) {
              _aside.find('dl:eq(0)').append('<dt>Font used above</dt>' +
                                             '<dd>'+cp_fonts[0]+'</dd>');
            }
          }
        });
      }
    }
  }

});

})(this, jQuery);
