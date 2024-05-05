import { LitElement, html, nothing } from 'lit';
import { customElement } from 'lit/decorators.js';
import { gettext as _ } from '../_i18n.ts';

@customElement('cp-menu')
export class CpMenu extends LitElement {
  connectedCallback() {
    super.connectedCallback();
    document.addEventListener('locale-ready', () => this.requestUpdate());
  }

  createRenderRoot() {
    return this;
  }

  render() {
    const query = (new URLSearchParams(location.search)).get('q') || '';
    const path = location.pathname;
    return html`
    <cp-dialog class="menu">
      <nav>
        <a href="/" aria-current="${path === '/'? 'page' : nothing}">
          <cp-icon icon="house"></cp-icon>
          ${_('start page')}</a>
        <a href="/scripts" aria-current="${path === '/scripts'? 'page' : nothing}">
          <cp-icon icon="scroll"></cp-icon>
          ${_('scripts')}</a>
        <a href="/search" aria-current="${path === '/search'? 'page' : nothing}">
          <cp-icon icon="magnifying-glass"></cp-icon>
          ${_('search')}</a>
        <a href="/analyze" aria-current="${path === '/analyze'? 'page' : nothing}">
          <cp-icon icon="chart-pie"></cp-icon>
          ${_('analyze')}</a>
        <a href="/random">
          <cp-icon icon="shuffle"></cp-icon>
          ${_('random page')}</a>
        <a href="/glossary" aria-current="${path === '/glossary'? 'page' : nothing}">
          <cp-icon icon="lightbulb"></cp-icon>
          ${_('glossary')}</a>
        <a href="/about" aria-current="${path === '/about'? 'page' : nothing}">
          <cp-icon icon="circle-question"></cp-icon>
          ${_('about this site')}</a>
      </nav>

      <form method="get" action="/search" role="search">
        <p>
          <label>
            ${_('Search code points:')}
            <input type="text" name="q" value="${query}" inputmode="search">
          </label>
          <button type="submit">${_('search')}</button>
        </p>
      </form>

      <section>
        <h2><cp-icon icon="gear" width="16px" height="16px"></cp-icon>&nbsp;${_('Settings')}</h2>
        <div class="card">
          <cp-darkmode></cp-darkmode>
        </div>
        <div class="card">
          <cp-language></cp-language>
        </div>
      </section>
    </cp-dialog>
    `;
  }

  show() {
    this.querySelector('cp-dialog').open();
  }

  close() {
    this.querySelector('cp-dialog').close();
  }
}
