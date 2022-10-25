import {gettext as _} from './_i18n.ts';

export function initGlossary(context) {
  const glossary = context.querySelector('#glossary');
  if (!glossary || glossary.dataset.upgraded) {
    return;
  }
  glossary.dataset.upgraded = true;
  const nav = document.createElement('ul');
  nav.className = 'glossary__quicknav';
  nav.innerHTML = `<li><a href="#top">${_('top')}</a></li>`;
  let lastLetter = ' ';
  glossary.querySelectorAll('dt[id]').forEach(dt => {
    if (! dt.previousElementSibling || dt.previousElementSibling.nodeName !== 'DD') {
      return;
    }
    const curLetter = dt.innerText.substr(0, 1).toUpperCase();
    if (curLetter.charCodeAt(0) > lastLetter.charCodeAt(0)) {
      lastLetter = curLetter;
      const subnav = document.createElement('li');
      const subnavA = document.createElement('a');
      subnavA.href= '#'+dt.id;
      subnavA.innerText = curLetter;
      subnav.appendChild(subnavA);
      nav.appendChild(subnav);
    }
  });
  glossary.parentNode.insertBefore(nav, glossary);
}
