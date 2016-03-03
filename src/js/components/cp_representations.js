'use strict';


import _ from '../toolbox/gettext';
import tools from '../toolbox/unicode';


export default function add_representations(cp) {
  var repr, secondary;

/**
 * register a representation of this codepoint with its name and
 * generator function
 */
function addRepr(name, formula) {
  var $el = $('<tr><th></th><td></td></tr>'),
      code = parseInt(repr.find('.repr-number').text(), 10);
  $el.find('th').text(name);
  $el.find('td').text(formula(code));
  repr.find('tbody').append($el.hide());
  secondary = secondary.add($el);
}

  repr = $('.representations', cp);

  if (repr.length) {
    secondary = $('tbody tr:not([class~="primary"])', repr).hide();
    var btn = $('<p style="text-align:center"><button type="button">'+_('show more')+'</button></p>')
              .on('click', function() {
                btn.find('button').text(secondary.is(':visible')?
                  _('show more') : _('hide')
                );
                secondary.toggle();
              }).insertAfter(repr);

    addRepr(_('RFC 5137'), function(n) {
      return '\\u\'' + tools.format_codepoint(n) + '\'';
    });

    addRepr(_('Python'), function(n) {
      var str = n.toString(16).toUpperCase(),
          pad = 4, chr = 'u';
      if (n > 0xFFFF) {
        pad = 8;
        chr = 'U';
      }
      while (str.length < pad) {
        str = "0" + str;
      }
      return '\\'+chr+str;
    });

    addRepr(_('Ruby'), function(n) {
      return '\\u{'+n.toString(16).toUpperCase()+'}';
    });

    addRepr(_('Perl'), function(n) {
      return '"\\x{'+n.toString(16).toUpperCase()+'}"';
    });

    addRepr(_('JavaScript, JSON and Java'), function(n) {
      return $.map(tools.codepoint_to_utf16(n), function(x) {
        return '\\u' + tools.format_codepoint(x);
      }).join('');
    });

    addRepr(_('C'), function(n) {
      var str = n.toString(16).toUpperCase(),
          pad = 4, chr = 'u';
      if (n > 0xFFFF) {
        pad = 8;
        chr = 'U';
      }
      while (str.length < pad) {
        str = "0" + str;
      }
      return '\\'+chr+str;
    });

    addRepr(_('CSS'), function(n) {
      var str = n.toString(16).toUpperCase();
      while (str.length < 6) {
        str = "0" + str;
      }
      return '\\'+str;
    });

    repr.on('click', 'td', function() {
      var range = document.createRange();
      range.selectNodeContents(this);
      var selection = window.getSelection();
      selection.removeAllRanges();
      selection.addRange(range);
    });

  }
}
