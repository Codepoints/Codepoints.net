/**
 * like element.closest() but piercing shadow DOM boundaries
 */
export function getClosest(node: Node, selector: string): Element|null {
  const closest = node.closest(selector);
  if (! closest) {
    const host = node.getRootNode().host;
    if (host) {
      return getClosest(host, selector);
    }
  }
  return closest;
}


/**
 * helper to get the maximum sensitivity level
 *
 * TODO fetch hard-coded value from server
 */
export function getMaxSensitivity() {
  return 3;
}
