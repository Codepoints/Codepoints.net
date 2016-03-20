'use strict';


import scroll_element from '../toolbox/scroll_element';


var handler_added = false;

/**
 * let in-page links scroll smooth
 */
export default function() {
  if (handler_added) {
    return;
  }
  handler_added = true;
  var $scroll_element = $(scroll_element);

  $(document)
    .on('click tap', 'a[href^="#"], a[rel~="internal"]', function(evt) {
      var $t, o;
      if (this.hash === '#_top') {
        o = 0;
      } else {
        $t= $(this.hash);
        if ($t.length) {
          o = $t.offset().top - 20;
        }
      }

      if (o !== undefined) {
        evt.preventDefault();
        $scroll_element.animate({scrollTop: o}, 1000);
      }
    });
}
