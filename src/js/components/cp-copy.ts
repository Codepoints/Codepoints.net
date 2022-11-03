import {LitElement, html} from 'lit';
import {customElement, property} from 'lit/decorators.js';
import {gettext as _} from '../_i18n.ts';

@customElement('cp-copy')
export class CpCopy extends LitElement {
  @property()
  content = '';

  render() {
    return html`<slot @click="${this._copy_content}">copy</slot>`;
  }

  private _copy_content() {
    navigator.clipboard.writeText(this.content).then(() => {
        const msg = document.createElement('cp-success');
        msg.innerText = _('copied');
        this.parentNode.insertBefore(msg, this.nextSibling);
    });
  }
}
