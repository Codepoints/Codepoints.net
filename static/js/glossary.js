/**
 * handle the large glossary better
 */
(function(window, $) {

  var gl = $('#glossary'),
      dts = gl.find('.last-special').nextAll('dt'),
      nav = $('<ul class="quicknav"></ul>'),
      p, t=$('<div></div>');

  var c = 0;
  nav.append($('<li><a href="#">\u21e7</a></li>').on('click', function() {
    window.scrollTo(0, 0);
    return false;
  }));
  dts.filter(function() {
    return $(this).prev('dd').length > 0;
  }).each(function() {
    var dt = $(this), t = dt.text().substr(0, 1).toUpperCase(),
        u = t.charCodeAt(0);
    if (u > c) {
      nav.append($('<li><a href="#'+dt.id+'">'+t+'</a></li>').on('click', function() {
        window.scrollTo(0, dt.offset().top - 30);
        return false;
      }));
      c = u;
    }
  });

  gl.before(nav);
  p = nav.offset().top;
  t.height(nav.outerHeight() + parseInt(nav.css('marginBottom'), 10)).hide().insertBefore(nav);

  $(window).on('scroll', function() {
    if (window.pageYOffset > p) {
      nav.addClass('floating');
      t.show();
    } else {
      nav.removeClass('floating');
      t.hide();
    }
  });

})(this, jQuery);
