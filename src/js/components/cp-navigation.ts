import { LitElement, css, html } from 'lit';
import { unsafeSVG } from 'lit/directives/unsafe-svg.js';
import { customElement } from 'lit/decorators.js';
import { gettext as _ } from '../_i18n.ts';
import IconMagnifyingGlass from '@fortawesome/fontawesome-free/svgs/solid/magnifying-glass.svg?raw';
import IconShuffle from '@fortawesome/fontawesome-free/svgs/solid/shuffle.svg?raw';

@customElement('cp-navigation')
export class CpNavigation extends LitElement {
  static styles = css`
    :host > slot > * {
      width: 25%;
      flex-grow: 1;
    }
    a, button, ::slotted(a), ::slotted(button) {
      display: block;
      width: auto;
      background: var(--color-back);
      text-align: center;
      color: var(--color-link);
      text-decoration: none;
    }
    a svg {
      display: block;
      width: 80%;
      height: 80%;
      max-width: 64px;
      max-height: 64px;
      margin: 1em auto 0;
      transition: transform .3s, color .3s;
      vertical-align: top;
      fill: currentColor;
    }

    a:focus > svg,
    a:hover > svg {
      transform: scale(1.33);
    }
  `;

  render() {
    const home = html`<a href="/">
      <span class="meta">${_('Home')}</span></a>`;
    const prev = html`<a href="/planes">
      <svg width="16" height="16"><svg viewBox="194 97 1960 1960" width="100%" height="100%"><use xlink:href="/static/images/unicode-logo-framed.svg#unicode" width="16" height="16"/></svg></svg>
      <span class="meta">${_('Code Points')}</span></a>`;
    const up = html`<a href="/search">
      ${unsafeSVG(IconMagnifyingGlass)}
      <span class="meta">${_('Search')}</span></a>`;
    const next = html`<a href="/random">
      ${unsafeSVG(IconShuffle)}
      <span class="meta">${_('Random')}</span></a>`;
    return html`
      <slot name="home">${home}</slot>
      <slot name="prev">${prev}</slot>
      <slot name="up">${up}</slot>
      <slot name="next">${next}</slot>
    `;
  }
}
