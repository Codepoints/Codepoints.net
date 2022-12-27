import {LitElement, html, nothing} from 'lit';
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
    return html`<svg width="${this.width || nothing}" height="${this.height || nothing}" fill="currentColor"><use href="${Icons}#${this.icon}"/></svg>`;
  }
}
