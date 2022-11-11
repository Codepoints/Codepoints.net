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
      box-shadow: inset 0 1px hsla(var(--hsl-highlight), .67),
                  0 1px hsla(var(--hsl-highlight), .67),
                  inset 1px 0 hsla(var(--hsl-highlight), .33),
                  inset -1px 0 hsla(var(--hsl-highlight), .33),
                  0 -1px 1px hsla(var(--hsl-backlight), .05);
      text-shadow: 0 1px var(--color-back-bright);
    }
    button:hover,
    button:focus {
      border-color: var(--color-border-medium);
      background: var(--color-back-bright);
      background: linear-gradient(to bottom, var(--color-back-bright), var(--color-back));
      box-shadow: inset 0 1px hsla(var(--hsl-highlight), .67),
                  0 0 3px 0px var(--color-hilite);
    }
    button:active {
      text-shadow: 0 1px hsl(var(--hsl-highlight));
      color: var(--color-text);
      box-shadow: inset 0 4px 4px -4px hsla(var(--hsl-backlight), .2),
                  inset 4px 0 4px -4px hsla(var(--hsl-backlight), .1),
                  inset -4px 0 4px -4px hsla(var(--hsl-backlight), .1);
      border: 1px solid var(--color-border-light);
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
  declare select: HTMLSelectElement;

  @property()
  declare value = '';

  connectedCallback() {
    super.connectedCallback();
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

  reset() {
    this.value = '';
    this.select.value = this.value;
    this.select.disabled = this.value === '';
  }
}
