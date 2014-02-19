/* jshint unused:false */
/**
 * add a scratchpad functionality
 */
define([
    'jquery',
    'components/gettext',
    'polyfills/fromcodepoint',
    'components/unicodetools',
    'zeroclipboard'
    ], function($, gettext, cp, tools, ZeroClipboard) {

  ZeroClipboard.setDefaults({
    moviePath: '/static/ZeroClipboard.swf'
  });

  var _ = gettext.gettext,
      scratchpad = [],
      scratchNode = $('<div class="scratchpad__container"></div>'),
      scratchCtrl = $('<div class="scratchpad__controls"></div>').appendTo(scratchNode),
      sp_max_length = 128,
      clip = new ZeroClipboard();

  /**
   *
   */
  function update_scratchpad() {
    if (scratchpad.length >= sp_max_length) {
      scratchpad = scratchpad.slice(sp_max_length - scratchpad.length + 1);
    }
    scratchpad.push(
      parseInt($('.payload').data('cp'), 10)
    );
    scratchNode.update();
  }

  /**
   *
   */
  function empty_scratchpad() {
    scratchpad = [];
    scratchNode.update();
  }


  /**
   *
   */
  function copy_scratchpad() {
    clip.setText("Copy me!");
  }
    clip.on('complete', function ( client, args ) {
        window.alert("Copied text to clipboard: " + args.text );
    });
    clip.on('dataRequested', function ( client, args ) {
      clip.setText("Copy me!");
    });


  /**
   *
   */
  function show_scratchpad() {
    var list = scratchpad.map(function(cp) {
      return 'U+'+tools.format_codepoint(cp);
    });
    window.location.href = '/'+list.join(',');
  }


  return {

    /**
     * this function initializes the scratchpad functionality
     */
    init: function() {

      /**
      * at the moment we don't provide a fallback for older
      * browsers or server-side syncing
      */
      if ('localStorage' in window) {
        var localStorage = window.localStorage;

        scratchpad = JSON.parse(localStorage.getItem('scratchpad'));

        if (! $.isArray(scratchpad)) {
          scratchpad = [];
        }

        var btn_empty = $('<button type="button" class="scratchpad__empty">'+_('empty')+'</button>')
                         .on('click', empty_scratchpad)
                         .appendTo(scratchCtrl);

        var btn_copy = $('<button type="button" class="scratchpad__copy">'+_('copy')+'</button>')
                         .on('click', copy_scratchpad)
                         .appendTo(scratchCtrl);
        //clip.glue(btn_copy);

        var btn_show = $('<button type="button" class="scratchpad__show">'+_('show')+'</button>')
                         .on('click', show_scratchpad)
                         .appendTo(scratchCtrl);

        scratchNode.update = function() {
          this.find('.data, .quiet').remove();
          if (scratchpad.length) {
            var ul = this.prepend('<ul class="data"></ul>').find('.data');
            $.each(scratchpad, function(i, v) {
              ul.append('<li><a class="cp" href="/U+'+tools.format_codepoint(v)+'">'+tools.format_codepoint(v)+'<span class="img">'+cp(v)+'</span></a></li>');
            });
            scratchCtrl.show();
          } else {
            this.prepend('<p class="quiet">'+_('You have no codepoints here yet. Add one by clicking “Add to scratchpad” on the details page.')+'</p>');
            scratchCtrl.hide();
          }
          return this;
        }.bind(scratchNode);

        var opener = $('<li class="scratchpad"><a href="#">Scratchpad</a></li>')
                      .on('click', function() {
                        scratchNode.update().dialog({
                          title: _('Scratchpad')
                        });
                        clip.reposition();
                        return false;
                      }).hide();
        $('.hd .primary').append(opener);
        opener.show('normal');

        var toolcontainer = $('.codepoint--tools');
        if (toolcontainer.length) {
          var btn = $('<p><button type="button" class="button button--hi"><i class="icon-edit"></i> '+_('Add to scratchpad')+'</button></p>')
            .on('click', update_scratchpad);
          toolcontainer.append(btn);
        }

        $(window).on('unload', function() {
          localStorage.setItem('scratchpad', JSON.stringify(scratchpad));
        });

      }

    } // END init()

  };
});
