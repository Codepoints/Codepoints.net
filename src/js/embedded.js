'use strict';


import jquery from 'jquery';
import load_font from './components/cp_font';


load_font($('.codepoint'));

var scr = document.createElement('script');
scr.src = 'https://stats.codepoints.net/piwik.js';
scr.async = true;
document.body.appendChild(scr);
