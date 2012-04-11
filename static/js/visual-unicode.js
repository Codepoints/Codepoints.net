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
  //$('nav .search, nav .about').wrapAll('<div class="nav-extra"></div>');

  /* display search form */
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
  $('.propsearch').each(function() {
    return false;
    var fieldset = $(this),
        area = $('<div class="propsearch-auto"><div class="inner"><input ' +
                 'type="text"/></div></div>'),
        input = area.find('input'),
        vals = [],
        map = {},
        labels = fieldset.find('label'),
        cbs = fieldset.find(':checkbox').each(function() {
          var val = this.value,
              lab = labels.filter('[for="'+this.id+'"]');
          if (lab.length) {
            lab = lab.text();
          } else {
            lab = val;
          }
          vals.push(lab);
          map[lab] = this.id;
        });
    fieldset.find('p').hide();
    area.appendTo(fieldset);
    input.on("keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).data( "autocomplete" ).menu.active ) {
          event.preventDefault();
        }
      })
    .autocomplete({
      source: function( request, response ) {
        // delegate back to autocomplete, but extract the last term
        response( $.ui.autocomplete.filter(
          vals, request.term.split(/\s*,\s*/).pop() ) );
      },
      focus: function() {
        // prevent value inserted on focus
        return false;
      },
      select: function( event, ui ) {
        var terms = $.trim(this.value).split(/\s*,\s*/);
        // remove the current input
        terms.pop();
        terms = $.grep(terms, function(n) {
          return n && $.inArray(n, vals) > -1;
        });
        // add the selected item
        terms.push( ui.item.value );
        cbs.each(function() { this.checked = false; });
        $.each(terms, function() {
          if (this in map) {
            cbs.filter('#'+map[this])[0].checked = true;
          }
        });
        // add placeholder to get the comma-and-space at the end
        terms.push( "" );
        this.value = terms.join( ", " );
        input.change();
        return false;
      },
      change: function( event, ui) {
        var terms = this.value.split(/\s*,\s*/);
        cbs.each(function() { this.checked = false; });
        $.each(terms, function() {
          if (this in map) {
            cbs.filter('#'+map[this])[0].checked = true;
          }
        });
      }
    });
  });

});

})(this, jQuery);
