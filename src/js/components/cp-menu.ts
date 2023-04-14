import { LitElement, css, html } from 'lit';
import { customElement } from 'lit/decorators.js';
import { gettext as _ } from '../_i18n.ts';

@customElement('cp-menu')
export class CpMenu extends LitElement {
  static styles = css`
  a {
    color: var(--color-link);
    transition: color .3s;
  }

  a:focus, a:hover {
    color: var(--color-link-hover);
  }

  h2 {
    margin: 0;
    font-size: 1.5em;
  }
  nav {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
  }
  nav > * {
    min-width: 8rem;
    text-align: center;
  }
  nav svg {
    height: 3rem;
    width: 3rem;
    display: block;
    fill: currentColor;
    fill-opacity: .5;
    margin: 0 auto .5rem;
    transition: fill-opacity .3s;
  }
  nav >*:focus svg,
  nav >*:hover svg {
    fill-opacity: 1;
  }
  cp-dialog > * + * {
    margin-top: 2rem;
  }

  .card {
    min-height: 2em;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  section {
    display: flex;
    flex-wrap: wrap;
    gap: .75em;
  }
  section > * {
    flex-grow: 1;
    flex-basis: 40%;
  }
  section > h2 {
    flex-grow: 0;
    flex-basis: 100%;
  }
  @media (max-width: 999px) {
    section {
      flex-direction: column;
    }
    section > * {
      flex-basis: 100%;
    }
  }
  `;

  render() {
    const query = (new URLSearchParams(location.search)).get('q') || '';
    return html`
    <link rel="stylesheet" href="${document.getElementById('main-css').href}">
    <cp-dialog class="menu">
      <nav>
        <a href="/">
          <cp-icon icon="house"></cp-icon>
          ${_('start page')}</a>
        <a href="/scripts">
          <cp-icon icon="scroll"></cp-icon>
          ${_('scripts')}</a>
        <a href="/search">
          <cp-icon icon="magnifying-glass"></cp-icon>
          ${_('search')}</a>
        <a href="/analyze">
          <cp-icon icon="chart-pie"></cp-icon>
          ${_('analyze')}</a>
        <a href="/random">
          <cp-icon icon="shuffle"></cp-icon>
          ${_('random page')}</a>
        <a href="/glossary">
          <cp-icon icon="lightbulb"></cp-icon>
          ${_('glossary')}</a>
        <a href="/about">
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
    this.renderRoot.querySelector('cp-dialog').open();
  }

  close() {
    this.renderRoot.querySelector('cp-dialog').close();
  }
}
