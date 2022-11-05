import {LitElement, css, html} from 'lit';
import {customElement, property} from 'lit/decorators.js';
import {unsafeSVG} from 'lit/directives/unsafe-svg.js';
import {gettext as _} from '../_i18n.ts';
import IconCircleQuestion from '@fortawesome/fontawesome-free/svgs/regular/circle-question.svg?raw';

let glossaryCache;


function fillGlossaryCache() {
    if (! glossaryCache) {
      const glossary = document.createElement('div');
      return fetch('/glossary')
        .then(response => response.text())
        .then(text => {
          glossary.innerHTML = text;
          glossaryCache = {};
          glossary.querySelectorAll('#glossary dt').forEach(dt => {
            let dd = dt.nextElementSibling;
            while (dd && dd.nodeName !== 'DD') {
              dd = dd.nextElementSibling;
            }
            glossaryCache[dt.id || dt.innerText] = dd? dd.innerHTML.replace(/href="#/, 'href="/glossary#') : '';
          });
        });
    }
    return Promise.resolve();
}


@customElement('cp-glossary-term')
export class CpGlossaryTerm extends LitElement {
  static styles = css`
    :host {
      position: relative;
    }
    .icon {
      margin-left: .5em;
      background: none;
      border: none;
      padding: 0;
    }
    .icon svg {
      width: 1.2em;
      height: 1.2em;
      vertical-align: top;
    }
    .icon path {
      fill: currentColor;
    }
    .info {
      position: absolute;
      z-index: 1;
      max-width: 350px;
      background: #000d;
      color: white;
      text-shadow: 0 1px black;
      padding: 0.5em;
      border-radius: 3px;
      box-shadow: 0 10px 5px -5px #0004;
      font: normal .8em var(--font-family-alternate);
    }
  `;

  @property()
  declare term = '';

  connectedCallback() {
    super.connectedCallback();
    this.term = this.getAttribute('term') || this.innerText;
    this._handleDocClick = this._handleDocClick.bind(this);
    document.addEventListener('click', this._handleDocClick);
  }

  disconnectedCallback() {
    document.removeEventListener('click', this._handleDocClick);
    super.disconnectedCallback();
  }

  render() {
    return html`
    <slot></slot>
    <button type="button" @click=${this._toggleInfo} class="icon" tabindex="0" aria-label="${_('show definition')}">${unsafeSVG(IconCircleQuestion)}</button>
    <div hidden class="info"></div>`;
  }

  _handleDocClick(event) {
    const info = this.renderRoot.querySelector('.info');
    if (event.target !== this && ! info.hidden) {
      this._hideInfo(info);
    }
  }

  _toggleInfo() {
    const info = this.renderRoot.querySelector('.info');
    if (info.hidden) {
      this._showInfo(info);
    } else {
      this._hideInfo(info);
    }
  }

  _showInfo(info) {
    fillGlossaryCache()
      .then(() => {
        info.innerHTML = glossaryCache[this.term] || _('no information found for this term');
        info.hidden = false;
      });
  }

  _hideInfo(info) {
    info.hidden = true;
  }
}
