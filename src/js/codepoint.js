'use strict';


import _ from './toolbox/gettext';
import add_representations from './components/cp_representations';
import load_font from './components/cp_font';
import Clipboard from 'clipboard';
import './polyfills/fromcodepoint';

/**
 * add the "copy to clipboard" button
 */
var $clip_button = $('<button>', {
  type: 'button',
  text: _('Copy to Clipboard'),
  'class': 'button button--hi',
})
  .prepend('<i class="icon-copy"></i>\u00A0')
  .wrap('<p>')
  .closest('p')
  .prependTo('.codepoint--tools');

var clipboard = new Clipboard($clip_button.get(0), {
  text: function(trigger) {
    return String.fromCodePoint($(trigger).closest('.codepoint').data('cp'));
  },
});

clipboard.on('success', function() {
  var offset = $clip_button.offset();
  var $tooltip = $('<div>', {
    text: _('Done!'),
    'class': 'tooltip',
  })
    .hide()
    .appendTo(document.body)
    .css({
      top: offset.top + 5 + $clip_button.outerHeight(),
      left: offset.left,
    })
    .fadeIn();
  window.setTimeout(() => $tooltip.fadeOut(() => $tooltip.remove()), 1000);
});


add_representations($('.codepoint'));
load_font($('.codepoint'));

/**
 * handle single codepoint's toolbox
 */
var $embed = $('.button--embed[data-link]');
var markup = $($embed.data('link'));
if (markup.length) {
  $embed.on('click', function() {
    markup.dialog({
      title: _('Embed this codepoint'),
      width: Math.min($(window).width(), 600),
      open: function() {
        var range = document.createRange();
        range.selectNodeContents(markup.find('pre')[0]);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
      }
    });
  });
}
