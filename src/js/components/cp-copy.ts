import {LitElement, html} from 'lit';
import {customElement, property} from 'lit/decorators.js';
import {gettext as _} from '../_i18n.ts';
import {getClosest, getMaxSensitivity} from '../_site-tools.ts';

@customElement('cp-copy')
export class CpCopy extends LitElement {
  @property()
  declare content = '';

  @property({ type: Boolean, reflect: true })
  declare disabled = false;

  connectedCallback() {
    super.connectedCallback();
    const sensitivity = Number(getClosest(this, '[data-sensitivity]')?.dataset.sensitivity);
    if (sensitivity && sensitivity === getMaxSensitivity()) {
      this.disabled = true;
      const btn = this.querySelector('button');
      if (btn) {
        btn.disabled = true;
      }
    }
  }

  render() {
    return html`<slot @click="${this._copy_content}">copy</slot>`;
  }

  private _copy_content() {
    if (this.disabled) {
      return;
    }
    navigator.clipboard.writeText(this.content).then(() => {
        const msg = document.createElement('cp-success');
        msg.innerText = _('copied');
        this.parentNode.insertBefore(msg, this.nextSibling);
    });
  }
}
