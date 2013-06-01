/**
 * replace the title attribute with a custom tooltip
 */
define(['jquery', 'jqueryui/position'], function($) {
var tipIDCounter = 0;

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
        id = '__tooltip_1AE511_' + (++tipIDCounter),
        arrow = $('<i></i>'),
        tip = $('<p class="tooltip"></p>').text(title).attr('id', id)
                .prepend(arrow)
                .hide().appendTo('body').on('mouseenter', function() {
                  $(this).hide(); });
    origin.removeAttr('title')
          .data('tooltip', tip)
          .attr('aria-labelledby', id)
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

    // display tooltip, if element is currently hovered
    if ("querySelector" in document) {
      var hovered = $(origin.parent()[0].querySelector(":hover"));
      if (hovered.index(origin) >= 0) {
        origin.trigger("mouseenter");
      }
    }
  });
  return this;
};
});
