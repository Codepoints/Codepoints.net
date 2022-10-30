import {LitElement, css, html} from 'lit';
import {customElement, property} from 'lit/decorators.js';
import {unsafeSVG} from 'lit/directives/unsafe-svg.js';
import IconPlus from '@fortawesome/fontawesome-free/svgs/solid/plus.svg?raw';
import IconMinus from '@fortawesome/fontawesome-free/svgs/solid/minus.svg?raw';
import IconAsterisk from '@fortawesome/fontawesome-free/svgs/solid/asterisk.svg?raw';
import { gettext as _ } from '../_i18n.ts';

@customElement('cp-search-boolean')
export class CpSearchBoolean extends LitElement {
  static styles = css`
    :host {
      display: flex;
    }
    button {
      user-select: none;
      font-family: var(--font-family-alternate);
      padding: .25em .5em;
      border-radius: 2px;
      transition-property: background, color, box-shadow, border-color;
      transition-duration: .3s;
      border: 1px solid var(--color-border);
      background: linear-gradient(to bottom, var(--color-back-bright), var(--color-back-dim));
      color: var(--color-text);
      box-shadow: inset 0 1px rgba(255,255,255,.67),
                  0 1px rgba(255,255,255,.67),
                  inset 1px 0 rgba(255,255,255,.33),
                  inset -1px 0 rgba(255,255,255,.33),
                  0 -1px 1px rgba(0,0,0,.05);
      text-shadow: 0 1px var(--color-back-bright);
    }
    button svg {
      width: 12px;
      height: 12px;
      margin-right: .3em;
    }
    .value-1 {
      background: linear-gradient(to bottom, var(--color-back-bright), var(--color-success-muted));
    }
    .value- svg {
      fill: var(--color-neutral);
    }
    .value-1 svg {
      fill: var(--color-success);
    }
    .value-0 {
      background: linear-gradient(to bottom, var(--color-back-bright), var(--color-danger-muted));
    }
    .value-0 svg {
      fill: var(--color-danger);
    }
  `;

  @property()
  select: HTMLSelectElement;

  @property()
  value: string = '';

  constructor() {
    super();
    this.select = this.querySelector('select');
    this.value = this.select.value;
    this.select.disabled = ! this.value;
  }

  render() {
    return html`
      <button
        class="value value-${this.value}"
        @click="${this._toggle}"
        title="${_('click to include or exclude this property')}">
        ${unsafeSVG(this.value === ''? IconAsterisk : this.value === '1'? IconPlus : IconMinus)}
        <slot name="desc"></slot>
      </button>
    `;
  }

  _toggle() {
    switch (this.value) {
      case '1':
        this.value = '0';
        break;
      case '0':
        this.value = '';
        break;
      case '':
        this.value = '1';
        break;
    }
    this.select.value = this.value;
    this.select.disabled = this.value === '';
  }
}
