/**
 * make paginated pages animate smoother
 */
define(['jquery'], function($) {

  var animationDuration = 2000/*ms*/;

  $.fn.enhancePagination = function() {

    this.on('click', '.cp-list .pagination a', function() {
      var $this = $(this),
          current_list = $this.closest('.cp-list'),
          current_page = current_list.data('page'),
          set = current_list.data('set'),
          mask = $('<div class="mask"></div>'),
          next_page = $this.closest('li').attr('value');
      if (! set) {
        set = [];
        current_list.data('set', set);
      }
      set[current_page] = current_list;

      current_list.append(mask).delay(200).promise().then(function() {
        $(this).addClass('waiting');
      });

      if (typeof set[next_page] !== 'undefined') {
        var next_list = set[next_page];
        handleSwitch(current_list, next_list, current_page > next_page);
      } else {
        $.get(this.href).then(function(data) {
          var next_list = $($.parseHTML(data)).find('.cp-list');
          set[next_page] = next_list;
          next_list.data('set', set);
          handleSwitch(current_list, next_list, current_page > next_page);
        });
      }

      return false;
    });

    return this;
  };

  function handleSwitch(current_list, next_list, reverse) {
    var revclass = reverse? ' reverse':'';
    current_list.find('.mask').remove();
    current_list.removeClass('waiting').addClass('exiting'+revclass);
    next_list.addClass('entering'+revclass).insertAfter(current_list);
    window.setTimeout(function() {
      next_list.removeClass('entering'+revclass);
      current_list.removeClass('exiting'+revclass).remove();
    }, animationDuration);
  }

});
