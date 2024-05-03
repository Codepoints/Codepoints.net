const catalog = {};

function loadTranslation(lang) {
  if (lang !== 'en' && ! (lang in catalog)) {
    fetch(`/static/locale/${lang}.json`)
      .then(resp => resp.json(), () => { throw new Error(); })
      .then(data => {
        catalog[lang] = data;
        document.dispatchEvent(new Event('locale-ready'));
      }, () => { console.log('problem loading translation'); });
  }
}

loadTranslation(document.documentElement.lang);

export function gettext(s) {
  const lang = document.documentElement.lang;
  if (catalog[lang] && catalog[lang][s]) {
    return catalog[lang][s];
  }
  return s;
}
