/**
 * close a <dialog> by clicking on the backdrop.
 *
 * @see https://stackoverflow.com/a/26984690/113195
 */
export function mixinBackdropClose(callback) {
  return function(event) {
    if (event.target.nodeName !== 'DIALOG') {
      return;
    }
    const rect = event.target.getBoundingClientRect();
    const isInDialog=(rect.top <= event.clientY && event.clientY <= rect.top + rect.height
      && rect.left <= event.clientX && event.clientX <= rect.left + rect.width);
    if (!isInDialog) {
      callback(event);
    }
  };
}
