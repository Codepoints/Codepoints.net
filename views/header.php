<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title><?php e($title)?> – Codepoints</title>
    <meta name="author" content="Manuel Strehl"/>
    <meta name="description" content="<?php e(isset($hDescription)? $hDescription : '')?>" />
    <!--[if lt IE 9]>
      <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="static/css/codepoints.css"/>
    <link rel="shortcut icon" type="image/vnd.microsoft.icon" href="static/images/favicon.ico"/>
    <link rel="search" href="opensearch.xml" type="application/opensearchdescription+xml" title="Search Codepoints" />
    <link rel="author" href="/humans.txt" />
    <script src="static/js/jquery.js"></script>
    <script src="static/js/jquery.ui.js"></script>
    <script src="static/js/visual-unicode.js"></script>
    <script type="text/javascript">
      WebFontConfig = {
        google: {
          families: [
            'Droid Serif:n,i,b,ib',
            'Droid Sans:n,b'
          ]
        }
      };
      (function() {
        var wf = document.createElement('script');
        wf.src = 'http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
        wf.type = 'text/javascript';
        wf.async = 'true';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(wf, s);
      })();
    </script>
    <?php echo isset($headdata)? $headdata : ''?>
  </head>
  <body>
    <div class="stage">
