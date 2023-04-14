import { LitElement, html } from 'lit';
import { customElement, property } from 'lit/decorators.js';
import { gettext as _ } from '../_i18n.ts';

/**
 * allow users to force-change the display mode
 *
 * the default is isDark==null. Any forced state will be persisted in a cookie,
 * so that the server can add an appropriate class to its response. We do this
 * to prevent a flash of dark/light content before JS can take over.
 */
@customElement('cp-darkmode')
export class CpDarkmode extends LitElement {
  @property()
  declare isDark = null;

  constructor() {
    super();
    this.isDark = null;
    if (document.documentElement.classList.contains('force-dark')) {
      this.isDark = true;
    } else if (document.documentElement.classList.contains('force-light')) {
      this.isDark = false;
    }
  }

  render() {
    const label = (
      this.isDark?
        _('switch to light mode (currently active: dark mode)'):
      this.isDark === false?
        _('switch to browser default (currently active: light mode)'):
        _('switch to dark mode (currently active: browser default)')
    );
    return html`
      <label>
        <input type="checkbox" @click="${this.choose}" .checked="${this.isDark}" .indeterminate="${this.isDark === null}">
        <strong>${_('dark / light mode:')}</strong> ${label}
      </label>
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
