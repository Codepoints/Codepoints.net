define(['polyfills/fromcodepoint'], function() {

  /**
   * calculate the surrogate pair for codepoints
   * beyond the BMP
   */
  function codepointToUTF16(cp) {
    var surrogates = [];
    if (cp > 0xFFFF) {
      surrogates.push(Math.floor((cp - 0x10000) / 0x400) + 0xD800);
      surrogates.push((cp - 0x10000) % 0x400 + 0xDC00);
    } else {
      surrogates.push(n);
    }
    return surrogates;
  }

  /**
   * take an integer and return an upper-case hex
   * representation with at least length 4
   */
  function formatCodepoint(cp) {
    var str = cp.toString(16).toUpperCase();
    while (str.length < 4) {
      str = "0" + str;
    }
    return str;
  }

  return {
    codepointToUTF16: codepointToUTF16,
    formatCodepoint: formatCodepoint
  };

});
