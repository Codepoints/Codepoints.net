'use strict';


/**
 * add keyboard navigation
 */
export default function() {
  $(document).on('keydown', function(e) {
    if (e.target !== document.body) {
      /* some other element received these (e.g. an <input>). Ignore. */
      return;
    }
    if (e.shiftKey && ! e.metaKey && ! e.ctrlKey && ! e.altKey) {
      var a = [], click = true;
      switch (e.which) {
        case 33: // PgDn: paginate back
          a = $('.pagination .prev a:eq(0)');
          break;
        case 34: // PgUp: paginate forth
          a = $('.pagination .next a:eq(0)');
          break;
        case 36: // Pos1: homepage
          a = $('a[rel="start"]:eq(0)');
          break;
        case 37: // ArrL: previous element
          a = $('a[rel="prev"]:eq(0)');
          break;
        case 38: // ArrU: containing block
          a = $('a[rel="up"]:eq(0)');
          break;
        case 39: // ArrR: next element
          a = $('a[rel="next"]:eq(0)');
          break;
        case 40: // ArrD: first child
          a = $('.data a:eq(0)');
          click = false;
          break;
        case 83: // S: search
          a = $('a[rel="search"]:eq(0)');
          break;
        case 65: // A: about
          a = $('nav .about a:eq(0)');
          break;
      }
      if (a.length) {
        a.trigger('focus');
        if (click) {
          window.location.href = a[0].href;
        }
        return false;
      }
    }
  });
}
