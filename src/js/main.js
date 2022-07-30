import 'vite/modulepreload-polyfill';

import barba from '@barba/core';
import anime from 'animejs/lib/anime.es.js';
import './_darkmode.js';
import { initGlossary } from './_glossary.js';

document.documentElement.classList.add('js');

function init(context) {
  initGlossary(context);
}

init(document.querySelector('[data-barba="container"]'));

barba.init({
  prefetchIgnore: '/search',
  transitions: [{
    name: 'baseline',
    enter(data) {
      init(data.next.container);
      return new Promise((resolve, reject) => resolve);
    },
  }]
});

barba.hooks.enter(() => {
  window.scrollTo(0, 0);
});
