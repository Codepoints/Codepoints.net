import {LitElement, css, html} from 'lit';
import {customElement, property} from 'lit/decorators.js';

@customElement('cp-success')
export class CpSuccess extends LitElement {
  static styles = css`
    :host {
      pointer-events: none;
      display: block;
      padding: .2em .8em;
      position: absolute;
      line-height: 1.6;
      z-index: 2;
      background: #000c;
      border-radius: .2em;
      box-shadow: 1px 1px 2px rgba(0,0,0,.4);
      color: white;
      text-shadow: 1px  1px 0 #000,
                   1px -1px 0 #000,
                  -1px  1px 0 #000,
                  -1px -1px 0 #000;
      animation-name: cp-success;
    }
    @keyframes cp-success {
      from {
        margin-top: -2em;
        opacity: 1;
      }
      to {
        margin-top: -4em;
        opacity: 0;
      }
    }
  `;

  @property()
  declare duration: int;

  render() {
    return html`<slot></slot>`;
  }

  connectedCallback() {
    super.connectedCallback();
    if (! this.duration) {
      this.duration = 1000;
    }
    this.style.animationDuration = this.duration + 'ms';
    window.setTimeout(() => this.remove(), this.duration);
  }
}
