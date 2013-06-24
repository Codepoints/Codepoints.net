define(function() {
  if (!String.prototype.codePointAt) {
    /**
     * ES6 Unicode Shims 0.1
     * (c) 2012 Steven Levithan <http://slevithan.com/>
     * MIT license
     */
    String.prototype.codePointAt = function (pos) {
      pos = isNaN(pos) ? 0 : pos;
      var str = String(this),
          code = str.charCodeAt(pos),
          next;
      // If a surrogate pair
      if (0xD800 <= code && code <= 0xDBFF && 0xDC00 <= next && next <= 0xDFFF) {
        next = str.charCodeAt(pos + 1);
        return ((code - 0xD800) * 0x400) + (next - 0xDC00) + 0x10000;
      }
      return code;
    };
  }

  return String.prototype.codePointAt;
});
