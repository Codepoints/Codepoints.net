import { LitElement } from 'lit';
import { customElement } from 'lit/decorators.js';

@customElement('cp-searchform')
export class CpSearchform extends LitElement {
  constructor() {
    super();
    this.addEventListener('reset', this.reset.bind(this));
  }

  createRenderRoot() {
    return this;
  }

  reset() {
    this.querySelectorAll('cp-search-boolean, cp-search-property').forEach(field => field.reset());
  }
}
