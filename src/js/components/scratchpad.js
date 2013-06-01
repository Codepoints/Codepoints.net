/**
 * add a scratchpad functionality
 */
define([
    'jquery',
    'components/gettext',
    'polyfills/fromcodepoint'
    ], function($, gettext, cp) {

  var _ = gettext.gettext;

  /**
   * at the moment we don't provide a fallback for older
   * browsers or server-side syncing
   */
  if ('localStorage' in window) {

    var scratchpad = JSON.parse(localStorage.getItem('scratchpad')),
        scratchNode = $('<div class="scratchpad__container"></div>'),
        scratchCtrl = $('<div class="scratchpad__controls"></div>').appendTo(scratchNode),
        sp_max_length = 128;

    if (! $.isArray(scratchpad)) {
      scratchpad = [];
    }

    $('<button type="button">'+_('empty scratchpad')+'</button>').on('click',
        function() {
          scratchpad = [];
          scratchNode.update();
        }).appendTo(scratchCtrl);

    scratchNode.update = function() {
      this.find('.data, .quiet').remove();
      if (scratchpad.length) {
        var ul = this.prepend('<ul class="data"></ul>').find('.data');
        $.each(scratchpad, function(i, v) {
          ul.append('<li><a class="cp" href="http://codepoints.net/U+'+v.toString(16).toUpperCase()+'">'+v.toString(16).toUpperCase()+'</a></li>');
        });
      } else {
        this.prepend('<p class="quiet">'+_('You have no codepoints here yet. Add one by clicking “Add to scratchpad” on the details page.')+'</p>');
      }
      return this;
    }.bind(scratchNode);

    var opener = $('<li class="scratchpad"><a href="#">Scratchpad</a></li>')
                  .on('click', function() {
                    scratchNode.update().dialog({
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
          scratchNode.update();
        });
      toolcontainer.append(btn);
    }

    $(window).on('unload', function() {
      localStorage.setItem('scratchpad', JSON.stringify(scratchpad));
    });

  }

});
