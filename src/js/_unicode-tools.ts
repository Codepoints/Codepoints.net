/**
 * calculate the surrogate pair for codepoints
 * beyond the BMP
 */
export function codepointToUtf16(cp) {
  var surrogates = [];
  if (cp > 0xFFFF) {
    surrogates.push(Math.floor((cp - 0x10000) / 0x400) + 0xD800);
    surrogates.push((cp - 0x10000) % 0x400 + 0xDC00);
  } else {
    surrogates.push(cp);
  }
  return surrogates;
}

export function intToHex(int: int) : string {
  let hex = int.toString(16);
  while (hex.length < 4) {
    hex = '0' + hex;
  }
  return hex.toUpperCase();
}
