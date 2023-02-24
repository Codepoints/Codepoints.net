import 'vite/modulepreload-polyfill';

import barba from '@barba/core';
import { initGlossary } from './_glossary.js';
import { trackByContent } from './_tracker.js';

import './components.js';

document.documentElement.classList.add('js');

function init(context) {
  initGlossary(context);

  /* embedded context? Open the full view in a new tab when
   * clicking on the image. */
  if (window.parent !== window) {
    document.querySelector('.sqfig').addEventListener('click', function() {
      window.open(location.href.replace(/\?embed/, ''), '_blank');
    });
  }
}

init(document.querySelector('[data-barba="container"]'));

const noAnimationQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

barba.init({
  prefetchIgnore: ['/search', '/random'],
  prevent: ({ href, event }) => {
    return event.defaultPrevented || /\/random$/.test(href);
  },
  transitions: [{
    name: 'baseline',
    sync: true,
    enter(data) {
      data.next.container.parentNode.style.minHeight = Math.max(data.next.container.offsetHeight, data.current.container.offsetHeight) + 'px';
      data.next.container.parentNode.classList.add('transition-active');
      init(data.next.container);
      const from = { opacity: 0 };
      const to = { opacity: 1 };
      if (! noAnimationQuery.matches) {
        switch (data.trigger.rel) {
          case 'next':
            from['transform'] = 'translate(3vw, 0)';
            to['transform'] = 'none';
            break;
          case 'prev':
          case 'previous':
            from['transform'] = 'translate(-3vw, 0)';
            to['transform'] = 'none';
            break;
          case 'parent':
          case 'up':
            from['transform'] = 'translate(0, -3vw)';
            to['transform'] = 'none';
            break;
          case 'child':
            from['transform'] = 'translate(0, 3vw)';
            to['transform'] = 'none';
            break;
        }
      }
      return data.next.container.animate([from, to], {
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

barba.hooks.after(data => trackByContent(data.next.container));
trackByContent(document);
