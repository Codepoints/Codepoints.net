export function intToHex(int: int) : string {
  let hex = int.toString(16);
  while (hex.length < 4) {
    hex = '0' + hex;
  }
  return hex;
}
