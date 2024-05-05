import {LitElement} from 'lit';
import {customElement, property} from 'lit/decorators.js';

@customElement('cp-success')
export class CpSuccess extends LitElement {
  @property()
  declare duration: int;

  createRenderRoot() {
    return this;
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
