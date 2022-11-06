import { LitElement, css, html } from 'lit';
import { customElement } from 'lit/decorators.js';
import {unsafeSVG} from 'lit/directives/unsafe-svg.js';
import IconXMark from '@fortawesome/fontawesome-free/svgs/solid/xmark.svg?raw';
import { gettext as _ } from '../_i18n.ts';
import { mixinBackdropClose } from '../_mixins.ts';

@customElement('cp-menu')
export class CpMenu extends LitElement {
  static styles = css`
  dialog {
    margin: 0;
    width: auto;
    min-height: 50vh;
    border: none;
    padding: 2rem;
    font-size: calc(1rem / var(--font-mod));
    font-family: var(--font-family-alternate);
  }
  dialog[open] {
    animation-name: cp-menu;
    animation-duration: .5s;
    animation-iteration-count: 1;
    animation-timing-function: ease-out;
  }
  @keyframes cp-menu {
    from {
      top: -20vh;
      opacity: .333;
    }
    to {
      top: 0;
      opacity: 1;
    }
  }
  @media (prefers-reduced-motion: reduce) {
    @keyframes cp-menu {
      from {
        opacity: .5;
      }
      to {
        opacity: 1;
      }
    }
  }
  .close {
    border: none;
    background: none;
    display: block;
    width: 42px;
    padding: 0;
    position: absolute;
    top: 1rem;
    right: 1rem;
  }
  `;

  render() {
    const query = (new URLSearchParams(location.search)).get('q') || '';
    return html`
    <dialog @click="${mixinBackdropClose(this.close.bind(this))}">
      <button type="button" class="close" @click="${this.close}">
        ${unsafeSVG(IconXMark.replace('<svg ', '<svg width="42px" height="42px" ').replace('<path ', '<path fill="currentColor" '))}
        <span>${_('close')}</span>
      </button>
      <a href="/">${_('go to the start page')}</a>
      <cp-darkmode></cp-darkmode>
      <cp-language></cp-language>

      <form method="get" action="/search">
        <p>
          <label>
            ${_('Search codepoints.net:')}
            <input type="text" name="q" value="${query}">
          </label>
          <button type="submit">${_('search')}</button>
        </p>
      </form>
    </dialog>
    `;
  }

  show() {
    this.renderRoot.querySelector('dialog').showModal();
    this.animation = this.renderRoot.querySelector('dialog').getAnimations()?.[0];
  }

  close() {
    if (this.animation) {
      this.animation.reverse();
      window.setTimeout(() => this.renderRoot.querySelector('dialog').close(), 500);
    } else {
      this.renderRoot.querySelector('dialog').close();
    }
  }
}
