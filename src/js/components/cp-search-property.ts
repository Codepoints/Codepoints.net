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
    .selected {
      background: linear-gradient(to bottom, var(--color-back-bright), var(--color-success-muted));
    }
    button svg {
      width: 12px;
      height: 12px;
      margin-right: .3em;
      fill: var(--color-text);
    }
    .selected svg {
      fill: var(--color-success);
    }
    dialog[open] > div {
      display: flex;
      flex-direction: column;
      flex-wrap: wrap;
      max-height: 400px;
    }
    dialog[open]::backdrop {
      backdrop-filter: blur(1px) grayscale(100%);
    }
  `;

  @property()
  declare checked = 0;

  @property()
  declare all = 0;

  @property()
  declare elements: Array;

  connectedCallback() {
    super.connectedCallback();
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
        class="${this.checked? 'selected' : ''}"
        @click="${this._choose}"
        title="${_('click to choose a value for this property')}">
        ${unsafeSVG(IconBars)}
        <slot name="desc"></slot>
        &nbsp;(${this.checked}/${this.all})
      </button>
      <dialog @close="${this._onclose}" @cancel="${this._onclose}">
        <button @click="${this._close}">${_('close')}</button>
        <button @click="${this._selectAll}">${_('select all')}</button>
        <button @click="${this._deselectAll}">${_('deselect all')}</button>
        <div>
          ${this.elements}
        </div>
      </dialog>
    `;
  }

  _selectAll() {
    this.elements.forEach(p => p.querySelector('input').checked = true);
  }

  _deselectAll() {
    this.elements.forEach(p => p.querySelector('input').checked = false);
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
