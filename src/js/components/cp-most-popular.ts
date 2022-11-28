import { LitElement, css, html } from 'lit';
import {customElement, property} from 'lit/decorators.js';
import {unsafeSVG} from 'lit/directives/unsafe-svg.js';
import { intToHex } from '../_unicode-tools.ts';


let mostPopular;

function getMostPopular() {
  if (! mostPopular) {
    mostPopular = fetch('/api/v1/popular')
      .then(response => response.json());
  }
  return mostPopular;
}


@customElement('cp-most-popular')
export class CpMostPopular extends LitElement {
  static styles = css`
  `;

  @property()
  declare mostPopular;

  createRenderRoot() {
    return this;
  }

  render() {
    if (! this.mostPopular) {
      return html`loading...`;
    }
    return html`
    <ol class="tiles">
      ${this.mostPopular.map(cp => html`
      <li>
        <a class="ln cp" href="/U+${intToHex(cp[0])}" data-cp="U+${intToHex(cp[0])}">
          ${unsafeSVG(cp[1])}
          <span class="title">${cp[2]}</span>
        </a>
      </li>`)}
    </ol>
    `;
  }

  connectedCallback() {
    super.connectedCallback();
    getMostPopular()
      .then(json => this.mostPopular = json);
  }
}
