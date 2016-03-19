<?php
$title = __('Codepoint of the Day: No Codepoint Found');
$hDescription = __('Every day a new codepoint. Unfortunately not specifically this day.');
$canonical = '/codepoint_of_the_day?date='.$date;
$headdata = '<link rel="alternate" type="application/atom+xml" href="' . q($router->getUrl('codepoint_of_the_day.xml')). '">';
include "header.php";
include "nav.php";
?>
<div class="payload dailycp">
  <aside class="other">
    <h2><?php _e('Codepoints of the Day')?></h2>
    <div id="ucotd_cal" data-date="<?php e(date('Y-m-d'))?>"></div>
  </aside>
  <h2><?php e($title)?></h2>
  <h1><?php _e('No Codepoint for this Date')?></h1>
  <section class="abstract">
    <p><?php _e('Unfortunately we cannot find a codepoint for this date.')?></p>
  </section>
  </div>
<?php
$footer_scripts = array(url("/static/js/dailycp.js"));
include "footer.php"?>
