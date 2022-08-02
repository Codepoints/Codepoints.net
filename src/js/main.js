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
    sync: true,
    enter(data) {
      data.next.container.parentNode.style.minHeight = Math.max(data.next.container.offsetHeight, data.current.container.offsetHeight) + 'px';
      data.next.container.parentNode.classList.add('transition-active');
      init(data.next.container);
      return anime({
        targets: data.next.container,
        opacity: [0, 1],
        duration: 300,
        easing: 'cubicBezier(0.455, 0.030, 0.015, 0.955)',
      }).finished.then(() => {
        data.next.container.parentNode.style.minHeight = '0';
        data.next.container.parentNode.classList.remove('transition-active');
      });
    },
    leave(data) {
      return anime({
        targets: data.current.container,
        opacity: [1, 0],
        duration: 300,
        easing: 'cubicBezier(0.455, 0.030, 0.515, 0.955)',
      }).finished.then(() => {
        data.current.container.remove();
      });
    },
  }]
});

barba.hooks.enter(() => {
  window.scrollTo(0, 0);
});
