<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8"/>
    <title><?php echo $title?></title>
    <script src="static/jquery.js"></script>
    <script src="static/json2.js"></script>
    <script src="static/underscore.js"></script>
    <script src="static/backbone.js"></script>
    <script src="static/visual-unicode.js"></script>
    <link rel="stylesheet" href="static/visual-unicode.css"/>
    <?php echo isset($headdata)? $headdata : ''?>
  </head>
  <body>
    <div class="stage">
