<p>
<label>
<input type="checkbox" id="darkmode">
<?=_q('dark mode?')?>
</label>
<script>
(function() {
var chk = document.getElementById('darkmode');
var root = document.documentElement;
chk.checked = root.classList.contains('force-dark');
chk.indeterminate = ! (root.classList.contains('force-dark') || root.classList.contains('force-light'));
chk.addEventListener('click', function() {
    if (root.classList.contains('force-dark')) {
        chk.checked = false;
        chk.indeterminate = false;
        root.classList.replace('force-dark', 'force-light');
        document.cookie = 'force_mode=light';
        updateIcon('light');
    } else if (root.classList.contains('force-light')) {
        chk.checked = false;
        chk.indeterminate = true;
        root.classList.remove('force-light');
        document.cookie = 'force_mode=;expires=Thu, 01 Jan 1970 00:00:00 GMT';
        updateIcon();
    } else {
        chk.checked = true;
        chk.indeterminate = false;
        root.classList.add('force-dark');
        document.cookie = 'force_mode=dark';
        updateIcon('dark');
    }
});
updateIcon();

function updateIcon(to) {
    if (! to) {
        to = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)? 'dark' : 'light';
        if (root.classList.contains('force-dark')) {
            to = 'dark';
        } else if (root.classList.contains('force-light')) {
            to = 'light';
        }
    }
    var old_url = to === 'light'? '/static/images/icon.dark.svg' : '/static/images/icon.svg';
    var new_url = to === 'light'? '/static/images/icon.svg' : '/static/images/icon.dark.svg';
    document.querySelectorAll('img[src="'+old_url+'"]').forEach(function(img) {
        img.src = new_url;
    });
}
})();
</script>
</p>
