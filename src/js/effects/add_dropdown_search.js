'use strict';


import _ from '../toolbox/gettext';


/**
 * display dropdown search form, when "search" nav item is clicked
 */
export default function() {
  var el = $('#footer_search');

  if (! el.data('extended')) {
    el.data('extended', true)
      .append(
        $('<p>')
          .append($('<a>', {
            href: '/search',
            text: _('Extended Search'),
          })))
      .append(
        $('<p>')
          .append($('<a>', {
            href: '/wizard',
            text: _('Find My Codepoint'),
          })));
  }

  function hide(evt) {
    if (evt.which === 27/* ESC */ ||
        (! el.find(evt.target).length &&
         $.inArray(evt.type, ['tap', 'click']) > -1)) {
      el.slideUp('normal');
    } else {
      $(document).one('tap click keydown', hide);
    }
  }

  $(document).on('click', 'nav a[rel="search"]', function(evt) {
    evt.preventDefault();

    el.show()
      .position({
        my: 'left top',
        at: 'left bottom',
        of: $(this),
        collision: 'fit'
      })
      .hide();

    if (el.is(':hidden')) {
      el.slideDown('normal')
        .find(':text:eq(0)')
          .focus();

      $(document).one('tap click keydown', hide);
    }
  });
}
