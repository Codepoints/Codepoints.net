'use strict';

/* a stripped down version of
 * https://mths.be/scrollingelement v1.5.1 by @diegoperini & @mathias | MIT license
 */

var scroll_element = document.body;

if (/^CSS1/.test(document.compatMode)) {
  /* standards mode: test, if the browser adheres to the spec */
  var iframe = document.createElement('iframe');
  iframe.style.height = '1px';
  (document.body || document.documentElement || document).appendChild(iframe);
  var doc = iframe.contentWindow.document;
  doc.write('<!DOCTYPE html><div style="height:9999em">x</div>');
  doc.close();

  if (doc.documentElement.scrollHeight > doc.body.scrollHeight) {
    scroll_element = document.documentElement;
  }

  iframe.parentNode.removeChild(iframe);
}

export default scroll_element;
