export default function(ctx) {
  ctx.querySelectorAll('details[id] > summary').forEach(node => {
    const id = node.parentNode.id;
    const a = document.createElement('a');
    a.href = '#'+id;
    a.classList.add('direct-link');
    a.textContent = '¶';
    a.ariaLabel = '<?=_q("direct link to this script")?>';
    a.addEventListener('click', () => node.parentNode.open = true);
    node.appendChild(a);
  });
  const hash = location.hash.replace(/^#/, '');
  if (hash) {
    const target = document.getElementById(hash);
    if (target && ('open' in target)) {
      target.open = true;
    }
  }
}
