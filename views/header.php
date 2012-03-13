<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8"/>
    <title><?php e($title)?></title>
    <script src="static/js/jquery.js"></script>
    <script src="static/js/jquery.ui.js"></script>
    <script src="static/js/json2.js"></script>
    <script src="static/js/underscore.js"></script>
    <script src="static/js/backbone.js"></script>
    <script src="static/js/visual-unicode.js"></script>
    <link rel="stylesheet" href="static/css/visual-unicode.css"/>
    <?php echo isset($headdata)? $headdata : ''?>
  </head>
  <body>
    <div class="stage">
