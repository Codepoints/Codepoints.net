import { LitElement, css, html } from 'lit';
import { customElement } from 'lit/decorators.js';

@customElement('cp-searchform')
export class CpSearchform extends LitElement {
  static styles = css`
    :host {
      display: block;
    }
  `;

  render() {
    return html`
      <slot></slot>
      <hr>
      <p>Quick intro:</p>
      <ol>
        <li>Choose properties to search for. The easiest is the “free search” field at the top where you can place any information that you have.</li>
        <li>Click on buttons with a ≡ to restrict the search to certain properties only. A dialog opens with possible options.</li>
        <li>Click on buttons with a * to enforce a specific yes/no property. Click again to search for code points <em>without</em> this property.</li>
        <li>Click “search” to start the search</li>
      </ol>
      <p>On code point detail pages you can click the values in the property description to be guided to a search page that shows code points with the same property.</p>
    `;
  }

  reset() {
    this.querySelectorAll('cp-search-boolean, cp-search-property').forEach(field => field.reset());
  }
}
