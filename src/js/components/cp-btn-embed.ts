import {LitElement, css, html} from 'lit';
import {customElement} from 'lit/decorators.js';
import {gettext as _} from '../_i18n.ts';
import { mixinBackdropClose } from '../_mixins.ts';
import { intToHex } from '../_unicode-tools.ts';

@customElement('cp-btn-embed')
export class CpBtnEmbed extends LitElement {
  static styles = css`
  .close {
    border: none;
    background: none;
    display: block;
    width: 42px;
    padding: 0;
    position: absolute;
    top: 1rem;
    right: 1rem;
  }
  `;

  render() {
    const cp = intToHex(parseInt(this.closest('[data-cp]').dataset.cp));
    return html`
      <slot @click="${this._show_instructions}">${_('embed this codepoint')}</slot>
      <dialog @click="${mixinBackdropClose(this.close.bind(this))}">
        <button type="button" class="close" @click="${this.close}">
          <cp-icon icon="xmark" width="42px" height="42px"></cp-icon>
          <span>${_('close')}</span>
        </button>
        <p>${_('Embed this codepoint in your own website by copy- and pasting the following HTML snippet:')}</p>
        <pre>&lt;iframe src="https://codepoints.net/U+${cp}?embed"
        style="width: 200px; height: 26px;
        border: 1px solid #444;">
&lt;/iframe></pre>
        <p>${_('If you want, you can freely change width and height to meet your needs. The layout will adapt accordingly.')}</p>
        <p>${_('On platforms that support the oEmbed standard, e.g. WordPress, embedding is even easier:')}
          ${_('Simply paste the web address of this page in the editor and the editor will do the rest:')}</p>
        <pre>${window.location}</pre>
      </dialog>
    `;
  }

  private _show_instructions() {
    this.renderRoot.querySelector('dialog').showModal();
    const range = document.createRange();
    range.selectNodeContents(this.renderRoot.querySelector('dialog pre'));
    const sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);
  }

  close() {
    this.renderRoot.querySelector('dialog').close();
  }
}
