import {LitElement, css, html} from 'lit';
import {customElement} from 'lit/decorators.js';
import {gettext as _} from '../_i18n.ts';
import { mixinBackdropClose } from '../_mixins.ts';

@customElement('cp-dialog')
export class CpDialog extends LitElement {
  static styles = css`
  .close {
    border: none;
    background: none;
    display: block;
    width: 42px;
    padding: 0;
    float: right;
    line-height: 1;
  }
  :host(.menu) dialog {
    margin: 0;
    width: auto;
    min-height: 50vh;
    border: none;
    padding: 2rem;
    font-size: calc(1rem / var(--font-mod));
    font-family: var(--font-family-alternate);
  }
  :host(.menu) dialog[open] {
    animation-name: cp-dialog-menu;
    animation-duration: .5s;
    animation-iteration-count: 1;
    animation-timing-function: cubic-bezier(0.33, 1, 0.68, 1);
  }
  @keyframes cp-dialog-menu {
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
    @keyframes cp-dialog-menu {
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
      <dialog autofocus @click="${mixinBackdropClose(this.close.bind(this))}">
        <form method="dialog">
          <button type="button" class="close" @click="${this.close}">
            <cp-icon icon="xmark" width="42px" height="42px"></cp-icon>
            <span>${_('close')}</span>
          </button>
        </form>
        <slot></slot>
      </dialog>
    `;
  }

  open() {
    this.renderRoot.querySelector('dialog').showModal();
    if (this.classList.contains('menu')) {
      this.animation = this.renderRoot.querySelector('dialog').getAnimations()?.[0];
    }
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
