<?php
$title = 'Codepoint of the Day: No Codepoint Found';
$hDescription = 'Every day a new codepoint. Unfortunately not specifically this day.';
include "header.php";
include "nav.php";
?>
<div class="payload dailycp">
  <aside class="other">
    <h2>Codepoints of the Day</h2>
    <div id="ucotd_cal" data-date="<?php e(date('Y-m-d'))?>"></div>
  </aside>
  <h2><?php e($title)?></h2>
  <h1>No Codepoint for this Date</h1>
  <section class="abstract">
    <p>Unfortunately we cannot find a codepoint for this date.</p>
  </section>
  </div>
<?php
$footer_scripts = array(
    "/static/js/dailycp.js"
);
include "footer.php"?>
