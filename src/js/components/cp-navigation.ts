import { LitElement, css, unsafeCSS, html } from 'lit';
import { customElement, property } from 'lit/decorators.js';
import { gettext as _ } from '../_i18n.ts';
import { customMedia } from '../media_queries.ts';

@customElement('cp-navigation')
export class CpNavigation extends LitElement {
  static styles = css`
    ul {
      list-style: none;
      margin: 0;
      padding-left: 0;
      display: flex;
      gap: 1px;
    }
    li {
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

  /**
   * icons by font awesome
   */
  render() {
    const home = html`<a href="/">
      <span class="meta">${_('Home')}</span></a>`;
    const prev = html`<a href="/planes">
      <svg width="16" height="16"><svg viewBox="194 97 1960 1960" width="100%" height="100%"><use xlink:href="/static/images/unicode-logo-framed.svg#unicode" width="16" height="16"/></svg></svg>
      <span class="meta">${_('Code Points')}</span></a>`;
    const up = html`<a href="/search">
      <svg viewBox="0 0 512 512"><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352c79.5 0 144-64.5 144-144s-64.5-144-144-144S64 128.5 64 208s64.5 144 144 144z"/></svg>
      <span class="meta">${_('Search')}</span></a>`;
    const next = html`<a href="/random">
      <svg viewBox="0 0 512 512"><path d="M403.8 34.4c12-5 25.7-2.2 34.9 6.9l64 64c6 6 9.4 14.1 9.4 22.6s-3.4 16.6-9.4 22.6l-64 64c-9.2 9.2-22.9 11.9-34.9 6.9s-19.8-16.6-19.8-29.6V160H352c-10.1 0-19.6 4.7-25.6 12.8L284 229.3 244 176l31.2-41.6C293.3 110.2 321.8 96 352 96h32V64c0-12.9 7.8-24.6 19.8-29.6zM164 282.7L204 336l-31.2 41.6C154.7 401.8 126.2 416 96 416H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H96c10.1 0 19.6-4.7 25.6-12.8L164 282.7zm274.6 188c-9.2 9.2-22.9 11.9-34.9 6.9s-19.8-16.6-19.8-29.6V416H352c-30.2 0-58.7-14.2-76.8-38.4L121.6 172.8c-6-8.1-15.5-12.8-25.6-12.8H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H96c30.2 0 58.7 14.2 76.8 38.4L326.4 339.2c6 8.1 15.5 12.8 25.6 12.8h32V320c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l64 64c6 6 9.4 14.1 9.4 22.6s-3.4 16.6-9.4 22.6l-64 64z"/></svg>
      <span class="meta">${_('Random')}</span></a>`;
    return html`
    <nav>
      <ul>
        <li><slot name="home">${home}</slot></li>
        <li><slot name="prev">${prev}</slot></li>
        <li><slot name="up">${up}</slot></li>
        <li><slot name="next">${next}</slot></li>
      </ul>
    </nav>`;
  }
}
