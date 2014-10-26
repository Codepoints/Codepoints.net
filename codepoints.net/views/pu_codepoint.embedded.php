<?php
$db = $router->getSetting('db');

$props = array(
    'age' =>     ($codepoint <= 0xF8FF? '1.1' : '2.0'),
    'na' =>      '',
    'JSN' =>     '',
    'gc' =>      'Co',
    'ccc' =>     '0',
    'dt' =>      'none',
    //'dm' =>      $codepoint,
    'nt' =>      'None',
    'nv' =>      '',
    'bc' =>      'L',
    'Bidi_M' =>  0,
    'bmg' =>     '',
    //'suc' =>     $codepoint,
    //'slc' =>     $codepoint,
    //'stc' =>     $codepoint,
    //'uc' =>      $codepoint,
    //'lc' =>      $codepoint,
    //'tc' =>      $codepoint,
    //'scf' =>     $codepoint,
    //'cf' =>      $codepoint,
    'jt' =>      'U',
    'jg' =>      'No_Joining_Group',
    'ea' =>      'A',
    'lb' =>      'XX',
    'sc' =>      'Zzzz',
    'Dash' =>    0,
    'WSpace' =>  0,
    'Hyphen' =>  0,
    'QMark' =>   0,
    'Radical' => 0,
    'Ideo' =>    0,
    'UIdeo' =>   0,
    'IDSB' =>    0,
    'IDST' =>    0,
    'hst' =>     'NA',
    'DI' =>      0,
    'ODI' =>     0,
    'Alpha' =>   0,
    'OAlpha' =>  0,
    'Upper' =>   0,
    'OUpper' =>  0,
    'Lower' =>   0,
    'OLower' =>  0,
    'Math' =>    0,
    'OMath' =>   0,
    'Hex' =>     0,
    'AHex' =>    0,
    'NChar' =>   0,
    'VS' =>      0,
    'Bidi_C' =>  0,
    'Join_C' =>  0,
    'Gr_Base' => 0,
    'Gr_Ext' =>  0,
    'OGr_Ext' => 0,
    'Gr_Link' => 0,
    'STerm' =>   0,
    'Ext' =>     0,
    'Term' =>    0,
    'Dia' =>     0,
    'Dep' =>     0,
    'IDS' =>     0,
    'OIDS' =>    0,
    'XIDS' =>    0,
    'IDC' =>     0,
    'OIDC' =>    0,
    'XIDC' =>    0,
    'SD' =>      0,
    'LOE' =>     0,
    'Pat_WS' =>  0,
    'Pat_Syn' => 0,
    'GCB' =>     'XX',
    'WB' =>      'XX',
    'SB' =>      'XX',
    'CE' =>      0,
    'Comp_Ex' => 0,
    'NFC_QC' =>  1,
    'NFD_QC' =>  1,
    'NFKC_QC' => 1,
    'NFKD_QC' => 1,
    'XO_NFC' =>  0,
    'XO_NFD' =>  0,
    'XO_NFKC' => 0,
    'XO_NFKD' => 0,
    'FC_NFKC' => '',
    'CI' =>      0,
    'Cased' =>   0,
    'CWCF' =>    0,
    'CWCM' =>    0,
    'CWKCF' =>   0,
    'CWL' =>     0,
    'CWT' =>     0,
    'CWU' =>     0,
    //'NFKC_CF' => $codepoint,
    'isc' =>     '',
    'na1' =>     '',
);

if ($codepoint <= 0xF8FF) {
    $block = new UnicodeBlock('Private Use Area', $db);
} elseif ($codepoint <= 0xFFFFF) {
    $block = new UnicodeBlock('Supplementary Private Use Area-A', $db);
} else {
    $block = new UnicodeBlock('Supplementary Private Use Area-B', $db);
}

$hex = sprintf('U+%04X', $codepoint);
?>
<!DOCTYPE html>
<html lang="<?php e($lang)?>">
  <head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title><?php e($hex)?> PRIVATE USE CODEPOINT – Codepoints</title>
    <meta name="author" content="Manuel Strehl"/>
    <!--[if lt IE 9]>
      <script src="/static/js/html5shiv.js!<?php e(CACHE_BUST)?>"></script>
    <![endif]-->
    <link rel="stylesheet" href="/static/css/embedded.css!<?php e(CACHE_BUST)?>"/>
    <link rel="author" href="/humans.txt" />
    <link rel="author" href="https://plus.google.com/107008580830183396063?rel=author" />
    <link rel="publisher" href="https://plus.google.com/115373008615574082246" />
    <link rel="canonical" href="http://codepoints.net<?php e($router->getUrl($hex))?>" />
  </head>
  <body class="embedded codepoint">
    <a target="_blank" href="http://codepoints.net<?php e($router->getUrl($hex))?>"
       title="<?php _e('View on Codepoints.net')?>">
      <figure>
        <span class="fig">&#xFFFD;</span>
      </figure>
      <div class="cp-head">
        <h1><span class="cp-code"><span></span><?php e($hex)?></span>
          <span class="cp-name">PRIVATE USE CODEPOINT</span></h1>
      </div>
    </a>
    <section class="info-section">
      <dl>
        <?php foreach(array('gc', 'sc', 'bc', 'dt', 'ea') as $cat):?>
          <dt><?php e($info->getCategory($cat))?></dt>
          <dd><a target="_blank" href="<?php e('search?'.$cat.'='.$props[$cat])?>"><?php e($info->getLabel($cat, $props[$cat]))?></a></dd>
        <?php endforeach?>
        <?php if($props['nt'] !== 'None'):?>
          <dt><?php _e('Numeric Value')?></dt>
          <dd><a target="_blank" href="<?php e('search?nt='.$props['nt'])?>"><?php e($info->getLabel('nt', $props['nt']).' '.$props['nv'])?></a></dd>
        <?php endif?>
      </dl>
    </section>
    <p class="note"><a target="_blank" href="http://codepoints.net<?php e($router->getUrl($hex))?>" rel="bookmark"><?php _e('» View this character on Codepoints.net')?></a></p>
<?php
    $trackingVars = array(
        array("mode","embedded","page"));
    include "partials/tracker.php";
?>
    <script>WebFontConfig={google:{families:['Droid Serif:n,i,b,ib','Droid Sans:n,b']}};</script>
    <script src="/static/js/embedded.js!<?php e(CACHE_BUST)?>"></script>
  </body>
</html>
