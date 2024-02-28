import {gettext as _} from '../_i18n.ts';

export default function(context) {
  const data = context.querySelector('#search_metadata');
  if (data) {
    const parsedData = JSON.parse(data.textContent);
    if ('script_age' in parsedData) {
      window.script_age = parsedData.script_age;
    }
    if ('region_to_block' in parsedData) {
      window.region_to_block = parsedData.region_to_block;
    }
  }
  context.addEventListener('click', (event) => {
    if (event.target.closest('button[type="reset"]')) {
      if (! confirm(_('Really remove all selected values?'))) {
        event.preventDefault();
        event.stopPropagation();
      }
    }
    if (event.target.closest('#wizard')) {
      context.querySelector('cp-wizard').hidden = false;
    }
  });
}
