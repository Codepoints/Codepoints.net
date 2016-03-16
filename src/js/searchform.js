'use strict';


import _ from './toolbox/gettext';


/* search form enhancement */
$('.extended.searchform').each(function() {
  var $form = $(this), fields = $('.propsearch, .boolsearch', $form).hide(),
      submitset = $('.submitset', $form),
      addlist = $('<ul class="query-add ui-widget ui-widget-content ' +
                  'ui-corner-all"></ul>').insertBefore(submitset),
      add = $('<p><button type="button">'+_('+ add new query')+'</button></p>')
              .insertBefore(submitset).find('button'),
      search_values = {},
      menu = $('<ul class="ui-menu ui-widget ui-widget-content" tabindex="0"></ul>');

  /** show an option dialog for a single prop */
  function _showPropertyChooser(property, values, callback) {
    var dlg = $('<ul class="query-choose"></ul>');
    $.each(values, function(i, value) {
      dlg.append($('<li><a class="button" href="#">'+value[1]+'</a></li>')
        .on('click tap', 'a', function() {
          // user selects an option from this category
          callback(property, value[1], value[0]);
          dlg.dialog('close').dialog('destroy').remove();
          dlg = null;
          return false;
        }));
    });
    dlg.dialog({
      title: property,
      modal: true,
      width: Math.min($(window).width(), 1030)
    });
  }

  /** create a single search field item */
  function _createItem(property, value, input) {
    var valclass = '', valclick = $.noop;
    if (input.is('select')) {
      valclass = ' y';
      valclick = function() {
        // switch value
        input.find('option[value=""]')[0].selected = false;
        if (input.find('option[value="1"]')[0].selected) {
          input.find('option[value="1"]')[0].selected = false;
          input.find('option[value="0"]')[0].selected = true;
          $(this).find('.value').addClass('n').removeClass('y');
        } else {
          input.find('option[value="0"]')[0].selected = false;
          input.find('option[value="1"]')[0].selected = true;
          $(this).find('.value').addClass('y').removeClass('n');
        }
        return false;
      };
    } else {
      valclick = function() {
        var that = $(this),
            values = search_values[property];
        _showPropertyChooser(property, values, function(property, value,
                                                        input2) {
          that.find('.value').text(value);
          input[0].checked = false;
          input2[0].checked = true;
        });
      };
    }
    return $('<li class="query-item"></li>')
      .html('<span class="key">'+property+':</span> <span class="value' +
            valclass + '" title="'+_('click to change')+'">' +
            value + '</span> <button type="button">'+_('remove')+'</button>')
        .find('button').button({
          text: false,
          icons: {primary: 'ui-icon-close', secondary: false}
        }).on('click tab', function() {
          // remove this field again
          var i = $(this).closest('li');
          if (input.is(':checkbox')) {
            input[0].checked = false;
          } else {
            input.find('option[value=""]')[0].selected = true;
          }
          i.slideUp('fast', i.remove);
          return false;
        }).end()
      .on('click tab', valclick).tooltip()
      .appendTo(addlist);
  }

  fields.filter('.propsearch').each(function() {
    var field = $(this), val = [],
        legend = $('legend', field).text().replace(/:\s*$/, '');
    $(':checkbox', field).each(function() {
      var chk = $(this), label = $('label[for="'+this.id+'"]', field).text();
      val.push([chk, label]);
      if (chk[0].checked) {
        _createItem(legend, label, chk);
      }
    });
    search_values[legend] = val;
  });
  search_values['Other Property'] = [];
  fields.filter('.boolsearch').each(function() {
    var sel = $('select', this), label = $('label', this).text();
    search_values['Other Property'].push([sel, label]);
    if (sel.find('option[value="1"]')[0].selected) {
      _createItem('Other Property', label, sel);
    }
  });
  $.each(search_values, function(k, v) {
    menu.append($('<li class="ui-menu-item"><a href="#">'+k+'</a></li>')
                .data('v', v).data('k', k));
  });

  $(document).on('keydown click tap', function() {
    menu.slideUp();
  });

  menu.css({
    display: 'none',
    position: 'absolute'
  }).appendTo('body').on('click tap', 'li', function() {
    // user selects a category from the menu
    var li = $(this);
    _showPropertyChooser(li.data('k'), li.data('v'),
      function(property, value, input) {
        if (input.is(':checkbox')) {
          input[0].checked = true;
        } else {
          input.find('option[value="1"]')[0].selected = true;
        }
        _createItem(property, value, input);
      });
    menu.slideUp();
    return false;
  }).on('keydown', function(e) {
    if (! e.shiftKey && ! e.metaKey && ! e.ctrlKey && ! e.altKey) {
      var focussed = menu.find('a:focus'),
          next = $();
      switch (e.which) {
        case 38: // ArrUp
          if (focussed.length) {
            next = focussed.parent().prev('li').find('a');
          }
          if (! next.length) {
            next = menu.find('a:last');
          }
          break;
        case 40: // ArrDown
          if (focussed.length) {
            next = focussed.parent().next('li').find('a');
          }
          if (! next.length) {
            next = menu.find('a:first');
          }
          break;
      }
      if (next.length) {
        next.focus();
        e.preventDefault();
        e.stopPropagation();
        return false;
      }
    }
  });

  add.on('click tap', function() {
    menu.show().position({
      my: 'left top',
      at: 'left bottom',
      of: this,
      collision: 'fit fit'
    }).hide().slideDown().focus();
    return false;
  });
  addlist.on('click tap', function(e) {
    if (e.target === this) {
      add.trigger('click');
      return false;
    }
  });

});
