import { LitElement, css, html } from 'lit';
import { customElement } from 'lit/decorators.js';
import { gettext as _ } from '../_i18n.ts';
import { mixinBackdropClose } from '../_mixins.ts';

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
  dialog {
    margin: 0;
    width: auto;
    min-height: 50vh;
    border: none;
    padding: 2rem;
    font-size: calc(1rem / var(--font-mod));
    font-family: var(--font-family-alternate);
  }
  dialog[open] {
    animation-name: cp-menu;
    animation-duration: .5s;
    animation-iteration-count: 1;
    animation-timing-function: ease-out;
  }
  @keyframes cp-menu {
    from {
      top: -20vh;
      opacity: .333;
    }
    to {
      top: 0;
      opacity: 1;
    }
  }
  @media (prefers-reduced-motion: reduce) {
    @keyframes cp-menu {
      from {
        opacity: .5;
      }
      to {
        opacity: 1;
      }
    }
  }
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
  `;

  render() {
    const query = (new URLSearchParams(location.search)).get('q') || '';
    return html`
    <dialog @click="${mixinBackdropClose(this.close.bind(this))}">
      <button type="button" class="close" @click="${this.close}">
        <cp-icon icon="xmark" width="42px" height="42px"></cp-icon>
        <span>${_('close')}</span>
      </button>
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

      <form method="get" action="/search">
        <p>
          <label>
            ${_('Search code points:')}
            <input type="text" name="q" value="${query}">
          </label>
          <button type="submit">${_('search')}</button>
        </p>
      </form>

      <section>
        <h2>
        <cp-icon icon="gear" width="16px" height="16px"></cp-icon>&nbsp;
        ${_('Settings')}</h2>
        <cp-darkmode></cp-darkmode>
        <cp-language></cp-language>
      </section>
    </dialog>
    `;
  }

  show() {
    this.renderRoot.querySelector('dialog').showModal();
    this.animation = this.renderRoot.querySelector('dialog').getAnimations()?.[0];
  }

  close() {
    if (this.animation) {
      this.animation.reverse();
      window.setTimeout(() => this.renderRoot.querySelector('dialog').close(), 500);
    } else {
      this.renderRoot.querySelector('dialog').close();
    }
  }
}
