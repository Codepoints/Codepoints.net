'use strict';


import _ from '../toolbox/gettext';


/**
 * display "to top" anchor if viewport < document
 *
 * Smooth scrolling will be handled by ./smooth_internal_links.
 */
export default function() {
  $(window).on("load", function() {
    if ($(window).height() + 50 < $(document).height()) {
      $('footer.ft nav ul:eq(0)').prepend(
        $('<li><a href="#_top" rel="internal"><i class="icon-chevron-up"></i> '+
          _('top')+
          '</a></li>'));
    }
  });
}
