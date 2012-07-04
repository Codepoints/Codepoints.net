/**
 * handle the large glossary better
 */
(function(window, $) {

  var gl = $('#glossary'),
      dts = gl.find('.last-special').nextAll('dt'),
      nav = $('<ul class="quicknav"></ul>'),
      top,
      placeholder = $('<div></div>'),
      floating_header_height = 0,
      c = 0;

  $(function() {
    // fix scrolling position later, when the header is floating
    var hd = $('header.hd');
    if (hd.is('.floating')) {
      floating_header_height = hd.outerHeight();
    }
  });

  nav.append($('<li><a href="#">\u21e7</a></li>').on('click', function() {
    window.scrollTo(0, 0);
    return false;
  }));
  dts.filter(function() {
    return $(this).prev('dd').length > 0;
  }).each(function() {
    var dt = $(this),
        t = dt.text().substr(0, 1).toUpperCase(),
        u = t.charCodeAt(0);
    // each <dt>, that starts with a new letter, adds to the glossary
    // navigation
    if (u > c) {
      nav.append($('<li><a href="#'+dt.id+'">'+t+'</a></li>').on('click', function() {
        window.scrollTo(0, dt.offset().top - 30 - floating_header_height);
        return false;
      }));
      c = u;
    }
  });

  gl.before(nav);
  top = nav.offset().top;
  placeholder.height(nav.outerHeight()).css({
    marginBottom: nav.css('marginBottom'),
    marginTop: nav.css('marginTop')
  }).hide().insertBefore(nav);

  $(window).on('scroll', function() {
    // make navigation floating
    if (window.pageYOffset > top - floating_header_height) {
      nav.addClass('floating');
      placeholder.show();
    } else {
      nav.removeClass('floating');
      placeholder.hide();
    }
  });

})(this, jQuery);
