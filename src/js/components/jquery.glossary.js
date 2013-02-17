/**
 * handle the display of glossary items
 *
 * depends on jQuery.cachedAjax
 */
define(['jquery', 'components/jquery.cachedajax'], function($){

  var glossary = null;

  $.fn.glossary = function() {
    var gl = this.find('.gl');
    if (gl.length) {
      $.cachedAjax('/glossary').done(function(data) {
        var $data = $(data);
        glossary = $data.find('#glossary');
        gl.each(function() {
          var $gl = $(this), dt, dd, win;
          dt = glossary.find('dt#'+$gl.data('term'));
          if (! dt.length) {
            return;
          }
          dt = dt.add(dt.nextUntil('dd')).add(dt.prevUntil('dd'));
          dd = dt.nextUntil('dt');
          win = $('<div class="tooltip glos"><dl></dl></div>')
                .find('dl').append(dt.clone()).append(dd.clone()).end()
                .on('mouseenter', function() { win.stop(true, true).appendTo('body').show(); })
                .on('mouseleave', function() { $gl.find('.after').trigger('mouseleave'); })
                .on('click tap', 'a[href^="#"]', function() { window.location.href = '/glossary' + this.hash; return false; });
          $gl.data('gl', win)
            .append('<span class="after">?</span>')
            .find('.after').on('mouseenter', function() {
              $gl.data('gl').stop(true, true).show().appendTo('body').position({
                my: 'left bottom',
                at: 'right top',
                of: $gl,
                offset: '10 0',
                collision: 'fit flip'
              });
            }).on('mouseleave', function() {
              $gl.data('gl').fadeOut(700, function() {
                $(this).detach();
              });
            });
        });
      });
    }
    return this;
  };

});
