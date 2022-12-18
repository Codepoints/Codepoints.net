import 'vite/modulepreload-polyfill';

import barba from '@barba/core';
import { initGlossary } from './_glossary.js';

/* eslint-disable @typescript-eslint/no-unused-vars */
import * as components from './components.js';
/* eslint-enable @typescript-eslint/no-unused-vars */

document.documentElement.classList.add('js');

function init(context) {
  initGlossary(context);
}

init(document.querySelector('[data-barba="container"]'));

barba.init({
  prefetchIgnore: ['/search', '/random'],
  prevent: ({ event }) => event.defaultPrevented,
  transitions: [{
    name: 'baseline',
    sync: true,
    enter(data) {
      data.next.container.parentNode.style.minHeight = Math.max(data.next.container.offsetHeight, data.current.container.offsetHeight) + 'px';
      data.next.container.parentNode.classList.add('transition-active');
      init(data.next.container);
      return data.next.container.animate([
        { opacity: 0 },
        { opacity: 1 },
      ], {
        duration: 300,
        iterations: 1,
        easing: 'cubic-bezier(0.455, 0.030, 0.515, 0.955)',
      }).finished.then(() => {
        data.next.container.parentNode.style.minHeight = '0';
        data.next.container.parentNode.classList.remove('transition-active');
      });
    },
    leave(data) {
      return data.current.container.animate([
        { opacity: 1 },
        { opacity: 0 },
      ], {
        duration: 300,
        iterations: 1,
        easing: 'cubic-bezier(0.455, 0.030, 0.515, 0.955)',
      }).finished.then(() => {
        data.current.container.remove();
      });
    },
  }]
});

barba.hooks.afterEnter((data) => {
  if (data.next.url.hash) {
    const el = document.getElementById(data.next.url.hash);
    if (el) {
      el.scrollIntoView(true);
      return;
    }
  }
  window.scrollTo(0, 0);
});
