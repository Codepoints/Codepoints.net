'use strict';


import _ from '../toolbox/gettext';


/**
 * make paginated pages animate smoother
 */
export default function() {
  if (document.referrer.search(new RegExp('^' + window.location.protocol +
      '//' + window.location.host + '/(wizard|search)\\?')) === 0) {
    $('<li>', {
      'class': 'up',
    })
      .append($('<a>', {
        href: document.referrer,
        text: _('Back to search results'),
      }))
      .insertAfter('.primary li.search');
  }
}
