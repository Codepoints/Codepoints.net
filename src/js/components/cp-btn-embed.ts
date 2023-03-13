import {LitElement, html} from 'lit';
import {customElement} from 'lit/decorators.js';
import {gettext as _} from '../_i18n.ts';
import { intToHex } from '../_unicode-tools.ts';

@customElement('cp-btn-embed')
export class CpBtnEmbed extends LitElement {
  render() {
    const cp = intToHex(parseInt(this.closest('[data-cp]').dataset.cp));
    return html`
      <slot @click="${this._show_instructions}">${_('embed this codepoint')}</slot>
      <cp-dialog>
        <p>${_('Embed this codepoint in your own website by copy- and pasting the following HTML snippet:')}</p>
        <pre>&lt;iframe src="https://codepoints.net/U+${cp}?embed"
        style="width: 200px; height: 26px;
        border: 1px solid #444;">
&lt;/iframe></pre>
        <p>${_('If you want, you can freely change width and height to meet your needs. The layout will adapt accordingly.')}</p>
        <p>${_('On platforms that support the oEmbed standard, e.g. WordPress, embedding is even easier:')}
          ${_('Simply paste the web address of this page in the editor and the editor will do the rest:')}</p>
        <pre>${window.location}</pre>
      </cp-dialog>
    `;
  }

  private _show_instructions() {
    this.renderRoot.querySelector('cp-dialog').open();
    const range = document.createRange();
    range.selectNodeContents(this.renderRoot.querySelector('cp-dialog pre'));
    const sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);
  }

  close() {
    this.renderRoot.querySelector('cp-dialog').close();
  }
}
