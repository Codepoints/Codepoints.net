define(['jquery', 'components/gettext'], function($, gettext) {

  var cp = $('.codepoint'),
      _ = gettext.gettext,
      repr, secondary;

  if (cp.length) {
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
        return '\\u\''+n.toString(16).toUpperCase()+'\'';
      });
      addRepr(_('Python'), function(n) {
        return '\\u'+n.toString(16).toUpperCase();
      });
      addRepr(_('Ruby'), function(n) {
        return '\\u{'+n.toString(16).toUpperCase()+'}';
      });
      addRepr(_('Perl'), function(n) {
        return '"\\x{'+n.toString(16).toUpperCase()+'}"';
      });
      addRepr(_('JavaScript, JSON and Java'), function(n) {
        if (n > 0xFFFF) {
          var suropairs = '\\u' + (Math.floor((n - 0x10000) / 0x400) + 0xD800).toString(16).toUpperCase();
          suropairs += '\\u' + ((n - 0x10000) % 0x400 + 0xDC00).toString(16).toUpperCase();
          return suropairs;
        } else {
          return '\\u'+n.toString(16).toUpperCase();
        }
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
    }
  }

  function addRepr(name, formula) {
    var $el = $('<tr><th></th><td></td></tr>'),
        code = parseInt(repr.find('.repr-number').text(), 10);
    $el.find('th').text(name);
    $el.find('td').text(formula(code));
    repr.find('tbody').append($el.hide());
    secondary = secondary.add($el);
  }

});
