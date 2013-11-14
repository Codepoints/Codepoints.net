require(['jquery',
  'components/gettext',
  'components/scratchpad',
  'jqueryui/dialog',
  'jqueryui/position',
  'components/jquery.cachedajax',
  'components/jquery.tooltip',
  'components/jquery.glossary',
  'components/dyn_pagination',
  'components/representations',
  'components/load_font'], function($, gettext, scratchpad) {

var _ = gettext.gettext;

if (document.referrer.match(/^https?:\/\/translate.google(usercontent)?.[a-z.]+(?:\/|$)/)) {
  if (! document.cookie.match(/(^|;)\s*notrans=true;/)) {
    $('body').addClass('no-fixed').prepend($('<p class="note">Howdy! We noticed, that you visit '+
        'this site through Google Translate. There are efforts to translate '+
        'Codepoints.net, but we need your help. Please visit <a href="https://crowdin.net/project/codepoints">the '+
        'translators’ page</a>, if you want to join us. <span class="close">Hide this message.</span></p>').on('click', '.close', function() {
          document.cookie = 'notrans=true; expires=Tue, 19 Jan 2038 03:14:07 GMT; path=/';
          $(this).closest('.note').slideUp();
          $('body').removeClass('no-fixed');
        }));
  }
}

scratchpad.init();

$.getScript('http://piwik.manuel-strehl.de/piwik.js');

$(function() {
  /**
  * Determine the scrolling element
  *
  * @see http://stackoverflow.com/questions/2837178
  */
  var scrollElement = (function () {
    var el = $(document.documentElement);
    if (el.scrollTop() > 0){
      return el;
    } else if(el.scrollTop(1).scrollTop() > 0) {
      return el.scrollTop(0);
    }
    return $('body');
  })();

  /** init tooltips */
  $(document).tooltip().glossary();

  /** init pagination enhancer */
  $('body').enhancePagination();

  /* scale the front headline text */
  var headline = $('.front h1'), resizer;
  if (headline.length) {
    // with hat tip to fittext.js
    resizer = function () {
      headline.css('font-size', Math.max(Math.min(headline.width() / 7.5,
                                                  160), 20));
    };
    resizer();
    $(window).on("load resize", resizer);
  }

  /** let in-page links scroll smooth */
  $(document).on('click tap', 'nav a[href^="#"], a[rel~="internal"]',
    function() {
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
        $('<li><a href="#top"><i class="icon-chevron-up"></i> '+_('top')+
        '</a></li>').find('a')
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
              $this.attr('href')).text(_('Extended Search'))))
             .append($('<p></p>').append($('<a></a>').attr('href',
              '/wizard').text(_('Find My Codepoint'))));
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


  /**
   * handle the "about" screen
   */
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
        $this.append('<p><a href="#'+$next[0].id+'"><i>'+_('Read on:')+
                     '</i> “'+$next.find('h1').text()+'”</a></p>');
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
   * make header floating, if window is higher than 500px
   */
  if ($(window).height() >= 750) {
    var hd = $('header.hd:eq(0)').addClass('floating'),
        hd_scrolled = true,
        hd_shadow = 0,
        gt_threshold = false;
    hd.next().css({
      marginTop: hd.outerHeight()
    });
    $(window).on('scroll', function() { hd_scrolled = true; });
    window.setInterval(function() {
      if (! hd_scrolled) { return; }
      hd_scrolled = false;
      var t = scrollElement.scrollTop();
      if (gt_threshold && t > 105) { return; }
      gt_threshold = false;
      if (t <= 15) { hd_shadow = 0;
      } else if (t <= 30) { hd_shadow = 1;
      } else if (t <= 45) { hd_shadow = 2;
      } else if (t <= 60) { hd_shadow = 3;
      } else if (t <= 75) { hd_shadow = 4;
      } else if (t <= 90) { hd_shadow = 5;
      } else              { hd_shadow = 6;
                            gt_threshold = true;
      }
      hd.css({
        boxShadow: '0 '+(hd_shadow-1)+'px '+hd_shadow+'px rgba(0,0,0,.2)'
      });
    }, 50);
  }

  /**
   * handle the EU cookie law thingy
   */
  var cook = $('.cookies');
  if (cook.length) {
    cook.append(" ").append($('<a href="#">'+_('Hide this message.')+'</a>')
    .click(function() {
      cook.fadeOut('slow');
      var date = new Date();
      date.setTime(date.getTime()+(2*365*24*60*60*1000));
      var expires = date.toGMTString();
      document.cookie = "_eu=hide; expires="+expires+"; path=/";
      return false;
    }));
  }

  /**
   * display latest blog post
   */
  var blog_preview = $('.blog-preview');
  if (blog_preview.length) {
    blog_preview.load('/blog-preview');
  }

  /**
   * display favorite CPs
   */
  var favorites = $('.favorites');
  if (favorites.length) {
    var fav_ul = $('<ul class="data"></ul>').appendTo(favorites);
    $.ajax({
      url: 'http://piwik.manuel-strehl.de/popular.php',
      dataType: 'jsonp'
    }).then(function(data) {
      favorites.find('.wait').remove();
      $.each(data, function() {
        var label = this.label.replace(/U /, ""),
            cp = $('<li><a class="cp" href="'+this.url+'">'+
                  label+'<img alt="" src="/api/v1/glyph/'+label+'">'+
                  '</a></li>');
        cp.one('mouseenter', function() {
          $.getJSON('/api/v1/codepoint/'+label+'?property=na').then(function(data) {
            cp.attr('title', data.na).tooltip();
          });
        });
        fav_ul.append(cp);
      });
    });
  }

  /**
   * handle single codepoint's toolbox
   */
  var $embed = $('.button--embed[data-link]');
  if ($embed.length) {
    var markup = $($embed.data('link'));
    if (markup.length) {
      $embed.on('click', function() {
        markup.dialog({
          title: _('Embed this codepoint'),
          width: Math.min($(window).width(), 600),
          open: function() {
            var range = document.createRange();
            range.selectNodeContents(markup.find('pre')[0]);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
          }
        });
      });
    }
  }

});

});
