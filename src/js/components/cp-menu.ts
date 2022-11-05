import { LitElement, css, html } from 'lit';
import { customElement } from 'lit/decorators.js';
import { gettext as _ } from '../_i18n.ts';

@customElement('cp-menu')
export class CpMenu extends LitElement {
  static styles = css`
  dialog {
    margin: 0;
    width: auto;
    min-height: 50vh;
    border: none;
    padding: 2rem;
  }
  dialog[open] {
    animation-name: menu;
    animation-duration: .5s;
    animation-iteration-count: 1;
    animation-timing-function: ease-out;
  }
  @keyframes menu {
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
    @keyframes menu {
      from {
        opacity: .5;
      }
      to {
        opacity: 1;
      }
    }
  }
  `;

  render() {
    return html`
    <dialog>
      <button type="button" @click="${this.close}">${_('close')}</button>
      <a href="/">${_('go to the start page')}</a>
      <cp-darkmode></cp-darkmode>
      <cp-language></cp-language>

      <form method="get" action="/search">
        <p>
          <label>
            ${_('Search codepoints.net:')}
            <input type="text" name="q" value="">
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
