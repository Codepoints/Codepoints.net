'use strict';


import scroll_element from '../toolbox/scroll_element';


/**
 * make header floating, if window is higher than some threshold
 */
export default function($header, threshold=750) {
  if ($(window).height() >= threshold) {
    var hd_scrolled = true,
        hd_shadow = 0,
        gt_threshold = false;

    $header
      .addClass('floating')
      .next()
        .css({
          marginTop: $header.outerHeight()
        });

    $(window).on('scroll', () => hd_scrolled = true);

    window.setInterval(function() {
      if (! hd_scrolled) { return; }
      hd_scrolled = false;
      var t = $(scroll_element).scrollTop();

      if (gt_threshold && t > 105) {
        /* return early, if there is no need to change box-shadow */
        return;
      }
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
      $header.css({
        boxShadow: '0 '+(hd_shadow-1)+'px '+hd_shadow+'px rgba(0,0,0,.2)'
      });
    }, 50);
  }
}
