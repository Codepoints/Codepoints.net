define(['polyfills/fromcodepoint',
    'polyfills/codepointat'],
    function(fromCodePoint, codePointAt) {

  /**
   * calculate the surrogate pair for codepoints
   * beyond the BMP
   */
  function codepoint_to_utf16(cp) {
    var surrogates = [];
    if (cp > 0xFFFF) {
      surrogates.push(Math.floor((cp - 0x10000) / 0x400) + 0xD800);
      surrogates.push((cp - 0x10000) % 0x400 + 0xDC00);
    } else {
      surrogates.push(cp);
    }
    return surrogates;
  }

  /**
   * take an integer and return an upper-case hex
   * representation with at least length 4
   */
  function format_codepoint(cp) {
    var str = cp.toString(16).toUpperCase();
    while (str.length < 4) {
      str = "0" + str;
    }
    return str;
  }

  /**
   * convert string to array of codepoints
   */
  function utf8_to_unicode(utf8) {
    var unicode = [], len= utf8.length, i, chunk;
    for (i = 0; i < len; i++) {
      chunk = utf8.substr(i, 1).charCodeAt(0);
      if (0xDC00 <= chunk && chunk <= 0xDFFF) {
        // surrogate pair's second half: skip, because it should've
        // been handled by the previous codePointAt call
        continue;
      }
      unicode.push(codePointAt.call(utf8, i));
    }
    return unicode;
  }


  /**
  * convert array of (int) codepoints to UTF-8 string
  */
  function unicode_to_utf8(unicode) {
    return fromCodePoint.apply(null, unicode);
  }


  /**
  * test, if a string is a possible codepoint
  *
  * Note: We don't test, if this is *really* a codepoint, i.e., connect to
  * the database
  */
  function maybe_codepoint(hexstring) {
      if (hexstring.length > 6 ||
          hexstring.search(/[^a-fA-F0-9]/) > -1 ||
          parseInt(hexstring, 16) > 0x10FFFF) {
          return false;
      }
      return true;
  }

  return {
    codepoint_to_utf16: codepoint_to_utf16,
    format_codepoint: format_codepoint,
    maybe_codepoint: maybe_codepoint,
    utf8_to_unicode: utf8_to_unicode,
    unicode_to_utf8: unicode_to_utf8
  };

});
