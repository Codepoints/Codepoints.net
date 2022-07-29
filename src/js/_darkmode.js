const chk = document.getElementById('darkmode');
const root = document.documentElement;

chk.checked = root.classList.contains('force-dark');
chk.indeterminate = ! (root.classList.contains('force-dark') || root.classList.contains('force-light'));

chk.addEventListener('click', function() {
  if (root.classList.contains('force-dark')) {
    chk.checked = false;
    chk.indeterminate = false;
    root.classList.replace('force-dark', 'force-light');
    document.cookie = 'force_mode=light;SameSite=Lax';
  } else if (root.classList.contains('force-light')) {
    chk.checked = false;
    chk.indeterminate = true;
    root.classList.remove('force-light');
    document.cookie = 'force_mode=;expires=Thu, 01 Jan 1970 00:00:00 GMT;SameSite=Lax';
  } else {
    chk.checked = true;
    chk.indeterminate = false;
    root.classList.add('force-dark');
    document.cookie = 'force_mode=dark;SameSite=Lax';
  }
});
