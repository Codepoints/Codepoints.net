'use strict';


import cached_ajax from '../toolbox/cached_ajax';


var animationDuration = 1000/*ms*/;

function handleSwitch(current_list, next_list, reverse) {
  var revclass = reverse? ' reverse':'';
  current_list.find('.mask')
              .remove();
  current_list.removeClass('waiting')
              .addClass('exiting'+revclass);
  next_list.addClass('entering'+revclass)
           .insertAfter(current_list)
           .tooltip();
  window.setTimeout(function() {
    next_list.removeClass('entering'+revclass);
    current_list.removeClass('exiting'+revclass)
                .remove();
  }, animationDuration);
}

/**
 * make paginated pages animate smoother
 */
export default function($ctx) {
  $ctx.on('click', '.cp-list .pagination a', function(evt) {
    evt.preventDefault();

    var $this = $(this),
        href = this.href,
        current_list = $this.closest('.cp-list'),
        current_page = current_list.data('page'),
        mask = $('<div class="mask"></div>'),
        next_page = $this.closest('li').attr('value');

    current_list.append(mask).delay(200).promise().then(function() {
      $(this).addClass('waiting');
    });

    cached_ajax(href).then(function(data) {
      var next_list = $($.parseHTML(data)).find('.cp-list');
      if (next_list.length) {
        if ('pushState' in window.history) {
          window.history.pushState({
            page: next_page
          },
          "",
          href);
        }
        if ('_paq' in window) {
          window._paq.push(['trackPageView']);
        }
        handleSwitch(current_list, next_list, current_page > next_page);
      }
    });
  });
}
