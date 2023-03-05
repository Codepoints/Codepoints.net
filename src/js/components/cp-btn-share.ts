import {LitElement, css, html, unsafeCSS} from 'lit';
import {customElement} from 'lit/decorators.js';
import {gettext as _} from '../_i18n.ts';
import {CpDialog} from './cp-dialog.ts';
import ShadowCSS from '../../css/shadow.css?url';

const services = {
  Whatsapp: 'whatsapp://send?text={title}%20{url}',
  Facebook: 'https://www.facebook.com/sharer/sharer.php?u={url}',
  tumblr: 'https://tumblr.com/widgets/share/tool?canonicalUrl={url}',
  reddit: 'https://reddit.com/submit?url={url}&title={title}',
  weibo: 'https://service.weibo.com/share/share.php?url={url}&title={title}',
  Telegram: 'https://t.me/share/url?url={url}',
  'e-mail': 'mailto:?subject={title}&body={url}',
};

@customElement('cp-btn-share')
export class CpBtnShare extends LitElement {
  _doShare(event) {
    const data = event.target.closest('a').href?.split('?');
    if (! data || data.length !== 2) {
      return;
    }
    event.preventDefault();
    const [title, url] = /^subject=([^&]+)&body=([^&]+)$/.exec(data[1]).slice(1).map(decodeURIComponent);
    if (! navigator.canShare || ! navigator.canShare({title, url})) {
      this.getDialog(title, url).then(dialog => { dialog.open(); });
      return;
    }
    navigator.share({title, url});
  }

  connectedCallback() {
    super.connectedCallback();
    this._doShare = this._doShare.bind(this);
    this.querySelector('a').addEventListener('click', this._doShare);
  }

  disconnectedCallback() {
    this.querySelector('a').removeEventListener('click', this._doShare);
    super.disconnectedCallback();
  }

  createRenderRoot() {
    return this;
  }

  render() {
    return;
  }

  private async getDialog(title, url) {
    let dialog = this.querySelector('cp-dialog');
    if (! dialog) {
      dialog = new CpDialog();
      const cp_share_container = new CpShareContainer();
      cp_share_container.title = title;
      cp_share_container.url = url;
      dialog.appendChild(cp_share_container);
      this.appendChild(dialog);
    }
    const promise = new Promise((resolve, reject) => {
      window.requestAnimationFrame(() => resolve(dialog));
    });
    return promise;
  }
}

@customElement('cp-share-container')
class CpShareContainer extends LitElement {
  static styles = css`
    .preview {
      padding: 1rem;
      border-bottom: 1px solid;
    }
    .preview span {
      display: block;
    }
    .preview .title {
      font-size: 1.25rem;
    }
    ul {
      padding-left: 0;
      list-style: none;
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
    }
  `;

  render() {
    const s = [];
    for (const service in services) {
      const url = services[service].replace('{url}', encodeURIComponent(this.url)).replace('{title}', encodeURIComponent(this.title));
      s.push(html`<li><a class="btn" href="${url}" target="_blank">${service}</a></li>`);
    }
    return html`
    <link rel="stylesheet" href="${ShadowCSS}">
    <div class="preview">
      <span class="url">${this.url}</span>
      <span class="title">${this.title}</span>
    </div>
    <p>${_('Share on:')}</p>
    <ul>
    ${s}
    </ul>`;
  }
}
