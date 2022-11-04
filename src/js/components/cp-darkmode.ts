import { LitElement, html } from 'lit';
import { customElement, property } from 'lit/decorators.js';
import { gettext as _ } from '../_i18n.ts';

@customElement('cp-darkmode')
export class CpDarkmode extends LitElement {
  @property()
  declare isDark = null;

  constructor() {
    super();
    if (document.documentElement.classList.contains('force-dark')) {
      this.isDark = true;
    } else if (document.documentElement.classList.contains('force-light')) {
      this.isDark = false;
    }
  }

  render() {
    return html`
      <p>
        <label>
          <input type="checkbox" @click="${this.choose}" .checked="${this.isDark}" .indeterminate="${this.isDark === null}">
          ${_('dark mode?')}
        </label>
      </p>
    `;
  }

  choose() {
    const root = document.documentElement;

    if (this.isDark) {
      this.isDark = false;
      root.classList.replace('force-dark', 'force-light');
      document.cookie = 'force_mode=light;SameSite=Lax';
    } else if (this.isDark === false) {
      this.isDark = null;
      root.classList.remove('force-light');
      document.cookie = 'force_mode=;expires=Thu, 01 Jan 1970 00:00:00 GMT;SameSite=Lax';
    } else {
      this.isDark = true;
      root.classList.add('force-dark');
      document.cookie = 'force_mode=dark;SameSite=Lax';
    }
  }
}
