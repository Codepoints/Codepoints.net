import {LitElement, html, css, nothing} from 'lit';
import {customElement, property} from 'lit/decorators.js';
import {unsafeHTML} from 'lit/directives/unsafe-html.js';
import {gettext as _} from '../_i18n.ts';
import {intToHex, codepointToUtf16} from '../_unicode-tools.ts';

function jsonify(n) {
  return codepointToUtf16(n).map(function(x) {
    return '\\u' + intToHex(x);
  }).join('');
}

function backslash_u_curly(n) {
  return '\\u{'+n.toString(16).toLowerCase()+'}';
}

function backslash_u_U(n) {
    let str = n.toString(16).toUpperCase(),
        pad = 4, chr = 'u';
    if (n > 0xFFFF) {
      pad = 8;
      chr = 'U';
    }
    while (str.length < pad) {
      str = "0" + str;
    }
    return '\\'+chr+str;
}

const formatters = [
  ['rfc5137', _('RFC 5137'), function(n) {
    return '\\u\'' + intToHex(n) + '\'';
  }],

  ['sh', _('Bash and Zsh'), backslash_u_U, _('inside <code>echo -e</code>')],

  ['c', _('C and C++'), backslash_u_U],

  ['csharp', _('C#'), backslash_u_U],

  ['css', _('CSS'), function(n) {
    let str = n.toString(16).toUpperCase();
    while (str.length < 6) {
      str = "0" + str;
    }
    return '\\'+str;
  }],

  ['go', _('Go'), backslash_u_U],

  ['js', _('JavaScript'), jsonify],
  ['mjs', _('Modern JavaScript'), backslash_u_curly, _('since ES6')],
  ['json', _('JSON'), jsonify],
  ['java', _('Java'), jsonify],

  ['pl', _('Perl'), function(n) {
    return '"\\x{'+n.toString(16).toUpperCase()+'}"';
  }],

  ['php', _('PHP'), backslash_u_curly],

  ['py', _('Python'), backslash_u_U],

  ['rb', _('Ruby'), backslash_u_curly],

  ['rs', _('Rust'), backslash_u_curly],
];


class FavoritesManager {
  constructor() {
    let personalized;
    try {
      personalized = JSON.parse(localStorage.getItem('cp-representations-favorites'));
      if (! Array.isArray(personalized)) {
        personalized = null;
      }
    } catch(e) {
      // ignore parse errors, personalized remains undefined
    }
    this.favorites = personalized || ['nr', 'utf-8', 'utf-16'];
  }
  has(fav) {
    return this.favorites.includes(fav);
  }
  add(fav) {
    if (! this.favorites.includes(fav)) {
      this.favorites.push(fav);
    }
    this._save();
  }
  remove(fav) {
    if (this.favorites.includes(fav)) {
      this.favorites = this.favorites.filter(item => item !== fav);
    }
    this._save();
  }
  _save() {
    localStorage.setItem('cp-representations-favorites', JSON.stringify(this.favorites));
  }
}


@customElement('cp-representations')
export class CpRepresentations extends LitElement {
  static styles = css`
:host > button {
  display: block;
  margin-top: .5rem;
}
@media print {
  :host button {
    display: none;
  }
}
:host > button,
.props {
  margin-left: auto;
  margin-right: auto;
}
th, td {
  padding: .2rem .5rem;
  vertical-align: top;
}
th:first-child {
  text-align: right;
}
th:last-child {
  text-align: left;
}
small {
  font-weight: normal;
  font-size: calc(1rem / var(--font-mod));
}
table:not(.show-all) tbody tr:not(.primary),
table:not(.show-all) tfoot {
  display: none;
}
.props button {
  opacity: .5;
  margin-left: .25rem;
}
.props button:focus,
.props button:hover {
  opacity: 1;
}
tbody small {
  display: block;
  margin-right: calc(1.6em * var(--font-mod));
}
  `;

  @property({ type: Number })
  declare cp = null;

  @property({ type: Boolean })
  declare allShown = false;

  @property({ type: FavoritesManager })
  declare favorites = null;

  @property({ type: Array })
  declare _representations = null;

  constructor() {
    super();
    this._representations = [];
    this.allShown = false;
  }

  connectedCallback() {
    super.connectedCallback();
    this.favorites = new FavoritesManager();
    this._thead = this.querySelector('thead');
    this._thead.querySelector('th:last-child').insertAdjacentHTML('beforeend',
      ` <small>${_('(click value to copy)')}</small>`);
    this.querySelectorAll('tbody tr').forEach(tr => {
      const label = tr.querySelector('th').textContent.trim();
      const value = tr.querySelector('td').textContent.trim();
      const system = tr.dataset.system;
      if (system && value) {
        this._representations.push({
          system,
          label,
          value,
          primary: this.favorites.has(system),
          description: null,
        });
      }
    });
    formatters.forEach(formatter => {
      this._representations.push({
        system: formatter[0],
        label: formatter[1],
        value: formatter[2](this.cp),
        primary: this.favorites.has(formatter[0]),
        description: formatter[3] ?? null,
      });
    });
  }

  render() {
    return html`
<table class="props representations ${this.allShown? 'show-all' : ''}">
  ${this._thead}
  <tbody>
    ${this._representations.map(obj => html`
      <tr class="${obj.primary? 'primary' : ''}" data-system="${obj.system}">
        <th scope="row">
          ${obj.label}
          <button type="button" @click="${() => this.togglePrimary(obj)}" title="${
            obj.primary?
              _('remove from favorites'):
              _('add to favorites (will be shown by default)')
          }"><cp-icon icon="${obj.primary? '' : 'regular-'}star" width="1em" height="1em"></cp-icon></button>
          <small>${unsafeHTML(obj.description || nothing)}</small>
        </th>
        <td><cp-copy content="${obj.value}">${obj.value}</cp-copy></td>
      </tr>
    `)}
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2">
<small>${_('Click the star button next to each label to set this representation as favorite or remove it from the favorites.')}
${_('Favorites will be shown initially.')}
${_('(Favorites are stored locally on your computer and never sent over the internet.)')}</small>
      </td>
    </tr>
  </tfoot>
</table>
<button type="button" @click="${this.toggle}">${this.allShown? _('hide all but favorites') : _('show more')}</button>
    `;
  }

  toggle() {
    this.allShown = ! this.allShown;
  }

  togglePrimary(obj) {
    if (! obj.system) {
      return;
    }
    if (this.favorites.has(obj.system)) {
      this.favorites.remove(obj.system);
      obj.primary = false;
    } else {
      this.favorites.add(obj.system);
      obj.primary = true;
    }
    this.requestUpdate();
  }
}
