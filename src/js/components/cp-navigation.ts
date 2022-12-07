import { LitElement } from 'lit';
import { customElement, property } from 'lit/decorators.js';
import { CpMenu } from './cp-menu.ts';

@customElement('cp-navigation')
export class CpNavigation extends LitElement {
  @property()
  declare menu: CpMenu;

  connectedCallback() {
    super.connectedCallback();
    this.showMenu = this.showMenu.bind(this);
    this.querySelector('[rel="start"]').addEventListener('click', this.showMenu);
  }

  disconnectedCallback() {
    this.querySelector('[rel="start"]').removeEventListener('click', this.showMenu);
    super.disconnectedCallback();
  }

  createRenderRoot() {
    return this;
  }

  render() {
    return;
  }

  showMenu(event) {
    event.preventDefault();
    if (! this.menu) {
      this.menu = new CpMenu();
      document.body.appendChild(this.menu);
    }
    window.requestAnimationFrame(this.menu.show.bind(this.menu));
  }
}
