'use strict';


import _ from './toolbox/gettext';
import add_representations from './components/cp_representations';
import load_font from './components/cp_font';


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
