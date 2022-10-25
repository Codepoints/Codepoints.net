import {LitElement, css, html} from 'lit';
import {customElement, property} from 'lit/decorators.js';
import {unsafeSVG} from 'lit/directives/unsafe-svg.js';
import IconBars from '@fortawesome/fontawesome-free/svgs/solid/bars.svg?raw';
import { gettext as _ } from '../_i18n.ts';

@customElement('cp-search-property')
export class CpSearchProperty extends LitElement {
  static styles = css`
    :host {
      display: flex;
    }
    button svg {
      width: 12px;
      height: 12px;
      margin-right: .3em;
      fill: var(--color-text);
    }
  `;

  @property()
  checked: number = 0;

  @property()
  all: number = 0;

  @property()
  elements;

  constructor() {
    super();
    this.all = this.querySelectorAll('input').length;
    this.checked = this.querySelectorAll('input:checked').length;
    this.elements = Array.prototype.map.call(this.querySelectorAll('p'), p => {
      const new_p = p.cloneNode(true);
      new_p.original = p;
      return new_p;
    });
  }

  render() {
    return html`
      <button
        class=""
        @click="${this._choose}"
        title="${_('click to choose a value for this property')}">
        ${unsafeSVG(IconBars)}
        <slot name="desc"></slot>
        &nbsp;(${this.checked}/${this.all})
      </button>
      <dialog @close="${this._onclose}" @cancel="${this._onclose}">
        <button @click="${this._close}">close</button>
        ${this.elements}
      </dialog>
    `;
  }

  _choose() {
    this.renderRoot.querySelector('dialog').showModal();
  }

  _close() {
    this.renderRoot.querySelector('dialog').close();
    this._onclose();
  }

  _onclose() {
    let checked = 0;
    this.elements.forEach(p => {
      checked += p.querySelector('input:checked')? 1 : 0;
      p.original.querySelector('input').checked = p.querySelector('input').checked;
    });
    this.checked = checked;
  }
}
