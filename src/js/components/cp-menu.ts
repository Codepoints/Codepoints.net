import { LitElement, css, html } from 'lit';
import { customElement } from 'lit/decorators.js';
import { gettext as _ } from '../_i18n.ts';

@customElement('cp-menu')
export class CpMenu extends LitElement {
  static styles = css`
  `;

  render() {
    return html`
    <dialog>
      <a href="/">${_('go to the start page')}</a>
      <cp-darkmode></cp-darkmode>
      <cp-language></cp-language>

      <form method="get" action="/search">
        <p>
          <label>
            ${_('Search codepoints.net:')}
            <input type="text" name="q" value="">
          </label>
          <button type="submit">${_('search')}</button>
        </p>
      </form>
    </dialog>
    `;
  }

  show() {
    this.renderRoot.querySelector('dialog').showModal();
  }
}
