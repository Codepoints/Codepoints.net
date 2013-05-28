/**
 * add a scratchpad functionality
 */
define([
    'jquery',
    'components/gettext',
    'polyfills/fromcodepoint'
    ], function($, gettext, cp) {

  var _ = gettext.gettext;

  if ('localStorage' in window) {

    var scratchpad = JSON.parse(localStorage.getItem('scratchpad')),
        sp_max_length = 128;

    if (! $.isArray(scratchpad)) {
      scratchpad = [];
    }

    var opener = $('<li class="scratchpad"><a href="#">Scratchpad</a></li>')
                  .on('click', function() {
                    $('<div>').text(cp(scratchpad)).dialog({
                      title: _('Scratchpad')
                    });
                    return false;
                  }).hide();
    $('.hd .primary').append(opener);
    opener.show('normal');

    var toolcontainer = $('.codepoint--tools');
    if (toolcontainer.length) {
      var btn = $('<p><button type="button" class="button button--hi"><i class="icon-edit"></i> '+_('Add to scratchpad')+'</button></p>')
        .on('click', function() {
          if (scratchpad.length >= sp_max_length) {
            scratchpad = scratchpad.slice(sp_max_length - scratchpad.length + 1);
          }
          scratchpad.push(
            parseInt($('.representations .repr-number').text(), 10)
          );
        });
      toolcontainer.append(btn);
    }

    $(window).on('unload', function() {
      localStorage.setItem('scratchpad', JSON.stringify(scratchpad));
    });

  }

});
