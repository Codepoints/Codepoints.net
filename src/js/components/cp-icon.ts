import {LitElement, html} from 'lit';
import {customElement, property} from 'lit/decorators.js';
import Icons from '../../images/icons.svg';

@customElement('cp-icon')
export class CpIcon extends LitElement {
  @property({ type: String })
  icon = '';

  @property({ type: String })
  width = '';

  @property({ type: String })
  height = '';

  connectedCallback() {
    super.connectedCallback();
  }

  createRenderRoot() {
    return this;
  }

  render() {
    const width = this.width.toString() || '';
    const height = this.height.toString() || '';
    return html`<svg width="${width}" height="${height}" fill="currentColor"><use href="${Icons}#${this.icon}"/></svg>`;
  }
}
