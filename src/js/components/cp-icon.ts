import {LitElement, html, nothing} from 'lit';
import {customElement, property} from 'lit/decorators.js';
import Icons from '../../images/icons.svg';

/**
 * render a specific icon from the sprite at src/images/icons.svg
 *
 * "icon" is the ID of the icon in the sprite
 * "width" and "height" are the size of the icon. Any value needs to match
 *     https://developer.mozilla.org/en-US/docs/Web/SVG/Content_type#length,
 *     which excludes especially rem.
 */
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
