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
    if (localStorage.getItem('scheme') === 'dark') {
      this.isDark = true;
    } else if (localStorage.getItem('scheme') === 'light') {
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
      root.dataset.scheme = 'light';
      localStorage.setItem('scheme', 'light');
    } else if (this.isDark === false) {
      this.isDark = null;
      root.dataset.scheme = (window.matchMedia('(prefers-color-scheme: dark)').matches? 'dark' : 'light');
      localStorage.removeItem('scheme');
    } else {
      this.isDark = true;
      root.dataset.scheme = 'dark';
      localStorage.setItem('scheme', 'dark');
    }
  }
}
