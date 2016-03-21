'use strict';


import 'jquery';
import 'jquery-ui/ui/position';
import 'jquery-ui/ui/dialog';
import 'jquery-ui/ui/datepicker';
import 'jquery-ui/ui/tooltip';
import 'jquery-ui/ui/accordion';
import 'jquery-ui/ui/slider';
import _ from './toolbox/gettext';
import smooth_internal_links from './effects/smooth_internal_links';
import add_to_top_link from './effects/add_to_top_link';
import add_dropdown_search from './effects/add_dropdown_search';
import floating_header from './effects/floating_header';
import keyboard_navigation from './effects/keyboard_navigation';
import better_pagination from './components/better_pagination';
import glossary_links from './components/glossary_links';
import back_to_search from './components/back_to_search';


function load(dependency) {
  return $.getScript(dependency);
}

load('https://stats.codepoints.net/piwik.js');

var $additional_scripts = $('#additional-scripts');
if ($additional_scripts.length) {
  $.each($additional_scripts.data('src'), (i, dep) => load(dep));
}

smooth_internal_links();
add_to_top_link();
add_dropdown_search();
floating_header($('header.hd'));
keyboard_navigation();
better_pagination($(document));
glossary_links($(document));
back_to_search();

$(document).tooltip();

/* ask for translation help */
if (document.referrer.match(/^https?:\/\/translate.google(usercontent)?.[a-z.]+(?:\/|$)/)) {
  if (! document.cookie.match(/(^|;)\s*notrans=true;/)) {
    $('body').addClass('no-fixed').prepend($('<p class="note">Howdy! We noticed, that you visit '+
        'this site through Google Translate. There are efforts to translate '+
        'Codepoints.net, but we need your help. Please visit <a href="https://crowdin.net/project/codepoints">the '+
        'translators’ page</a>, if you want to join us. <span class="close">Hide this message.</span></p>').on('click', '.close', function() {
          document.cookie = 'notrans=true; expires=Tue, 19 Jan 2038 03:14:07 GMT; path=/';
          $(this).closest('.note').slideUp();
          $('body').removeClass('no-fixed');
        }));
  }
}

/* handle the EU cookie law thingy */
var cook = $('.cookies');
if (cook.length) {
  cook.append(" ").append($('<a href="#">'+_('Hide this message.')+'</a>')
  .click(function() {
    cook.fadeOut('slow');
    var date = new Date();
    date.setTime(date.getTime()+(2*365*24*60*60*1000));
    var expires = date.toGMTString();
    document.cookie = "_eu=hide; expires="+expires+"; path=/";
    return false;
  }));
}
