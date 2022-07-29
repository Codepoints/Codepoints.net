import 'vite/modulepreload-polyfill';

import barba from '@barba/core';
import anime from 'animejs/lib/anime.es.js';
import './_darkmode.js';

document.documentElement.classList.add('js');

barba.init({
  prefetchIgnore: '/search',
});

barba.hooks.enter(() => {
  window.scrollTo(0, 0);
});
