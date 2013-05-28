define(function() {
  if (!String.fromCodePoint) {
      /*!
       * ES6 Unicode Shims 0.1
       * (c) 2012 Steven Levithan <http://slevithan.com/>
       * MIT License
       */
      String.fromCodePoint = function fromCodePoint () {
          var chars = [], point, offset, units, i;
          for (i = 0; i < arguments.length; ++i) {
              point = arguments[i];
              offset = point - 0x10000;
              units = point > 0xFFFF ? [0xD800 + (offset >> 10), 0xDC00 + (offset & 0x3FF)] : [point];
              chars.push(String.fromCharCode.apply(null, units));
          }
          return chars.join("");
      };
  }

  return String.fromCodePoint;
});
