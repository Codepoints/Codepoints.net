<?php
$title = 'Scripts';
$hDescription = 'Browse Codepoints.net by script';
$headdata = '
    <style type="text/css">

svg {
  width: 1280px;
  height: 800px;
  pointer-events: all;
}

circle {
  fill: #dbe4f0;
}

path {
  fill: #aaa;
  stroke: #fff;
  cursor: pointer;
}

    </style>
    ';
include 'header.php';
include 'nav.php';
?>
<div class="payload static script">
  <h1>Browse Codepoints by Script</h1>
  <div id="body">
    <svg xmlns="http://www.w3.org/2000/svg" id="earth">
      <defs>
        <radialGradient id="reflect"
          r="75%" cx="35%" cy="20%">
          <stop stop-color="white" stop-opacity=".67" offset="0" />
          <stop stop-color="white" stop-opacity="0.0" offset=".3" />
          <stop stop-color="white" stop-opacity="0.0" offset=".8" />
          <stop stop-color="black" stop-opacity="0.2" offset="1" />
        </radialGradient>
      </defs>
      <circle cx="50%" cy="50%" r="35.5%" style="cursor: move" />
      <circle id="athmo" cx="50%" cy="50%" r="35.5%" style="pointer-events: none; fill: url(#reflect)" />
    </svg>
  </div>
</div>
<?php
$footer_scripts = array(
    '/static/js/d3.js',
    '/static/js/d3.geo.js',
    '/static/js/scripts.js',
);
include 'footer.php'?>
