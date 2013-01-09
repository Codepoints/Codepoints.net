<?php
$props = $codepoint->getProperties();
$block = $codepoint->getBlock();
?>
<!DOCTYPE html>
<html lang="<?php e($lang)?>">
  <head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>U+<?php e($codepoint->getId('hex'). ' ' . $codepoint->getName())?> – Codepoints</title>
    <meta name="author" content="Manuel Strehl"/>
    <!--[if lt IE 9]>
      <script src="/static/js/html5shiv.js!<?php e(CACHE_BUST)?>"></script>
    <![endif]-->
    <link rel="stylesheet" href="/static/css/embedded.css!<?php e(CACHE_BUST)?>"/>
    <link rel="author" href="/humans.txt" />
    <link rel="author" href="https://plus.google.com/107008580830183396063?rel=author" />
    <link rel="publisher" href="https://plus.google.com/115373008615574082246" />
    <link rel="canonical" href="http://codepoints.net<?php e($router->getUrl($codepoint))?>" />
  </head>
  <body class="embedded codepoint">
    <a target="_blank" href="http://codepoints.net<?php e($router->getUrl($codepoint))?>"
       title="<?php _e('View on Codepoints.net')?>">
      <figure>
        <span class="fig"><?php e($codepoint->getSafeChar())?></span>
        <?php $fonts = $codepoint->getFonts();
        if (count($fonts)):?>
          <datalist id="fonts">
            <?php foreach ($fonts as $font):?>
              <option value="<?php e($font['id'])?>"><?php e($font['font'])?></option>
            <?php endforeach?>
          </datalist>
        <?php endif?>
      </figure>
      <h1><span class="cp-code"><span>U+</span><?php e($codepoint->getId('hex'))?></span>
      <span class="cp-name"><?php e($codepoint->getName())?></span></h1>
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
    <section class="prop-section">
      <table class="props">
        <thead>
          <tr>
            <th><?php _e('System')?></th>
            <th><?php _e('Representation')?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th><?php _e('Nº')?></th>
            <td><?php e($codepoint->getId())?></td>
          </tr>
          <tr>
            <th><?php _e('UTF-8')?></th>
            <td><?php e($codepoint->getRepr('UTF-8'))?></td>
          </tr>
          <tr>
            <th><?php _e('UTF-16')?></th>
            <td><?php e($codepoint->getRepr('UTF-16'))?></td>
          </tr>
          <tr>
            <th><?php _e('UTF-32')?></th>
            <td><?php e($codepoint->getRepr('UTF-32'))?></td>
          </tr>
          <tr>
            <th><?php _e('URL-Quoted')?></th>
            <td>%<?php e($codepoint->getRepr('UTF-8', '%'))?></td>
          </tr>
          <tr>
            <th><?php _e('HTML-Escape')?></th>
            <td>&amp;#x<?php e($codepoint->getId('hex'))?>;</td>
          </tr>
          <?php $alias = $codepoint->getALias();
          foreach ($alias as $a):?>
            <tr>
              <th><?php if ($a['type'] === 'html') {
                _e('HTML-Escape');
              } else {
                e($a['type']);
              }?></th>
              <td><?php if ($a['type'] === 'html') {
                echo '&amp;';
              }
              e($a['alias']);
              if ($a['type'] === 'html') {
                echo ';';
              }?></td>
            </tr>
          <?php endforeach?>
          <?php $pronunciation = $codepoint->getPronunciation();
          if ($pronunciation):?>
            <tr>
              <th>Pīnyīn</th>
              <td><?php e($pronunciation)?></td>
          </tr>
          <?php endif?>
          <?php foreach (array('kIRG_GSource', 'kIRG_HSource', 'kIRG_JSource',
            'kIRG_KPSource', 'kIRG_KSource', 'kIRG_MSource', 'kIRG_TSource',
            'kIRG_USource', 'kIRG_VSource', 'kBigFive', 'kCCCII', 'kCNS1986',
            'kCNS1992', 'kEACC', 'kGB0', 'kGB1', 'kGB3', 'kGB5', 'kGB7', 'kGB8',
            'kHKSCS', 'kIBMJapan', 'kJis0', 'kJIS0213', 'kJis1', 'kKPS0', 'kKPS1',
            'kKSC0', 'kKSC1', 'kMainlandTelegraph', 'kPseudoGB1',
            'kTaiwanTelegraph', 'kXerox') as $v):
          if ($props[$v]):?>
            <tr>
              <th><?php e($info->getCategory($v))?></th>
              <td><?php e($props[$v])?></td>
            </tr>
          <?php endif; endforeach?>
        </tbody>
      </table>
    </section>
    <p class="note"><a target="_blank" href="http://codepoints.net<?php e($router->getUrl($codepoint))?>" rel="bookmark"><?php _e('» View this character on Codepoints.net')?></a></p>
    <script id="_ts">var _paq=_paq||[];(function(){var u="http://piwik.manuel-strehl.de/";_paq.push(['setSiteId',4]);_paq.push(['setTrackerUrl',u+'piwik.php']);_paq.push(['trackPageView']);_paq.push(['enableLinkTracking']);var d=document,g=d.createElement('script'),s=d.getElementsByTagName('script')[0];g.type='text/javascript';g.defer=true;g.async=true;g.src=u+'piwik.js';s.parentNode.insertBefore(g,s);})();</script>
    <script>WebFontConfig={google:{families:['Droid Serif:n,i,b,ib','Droid Sans:n,b']}};</script>
    <script src="/static/js/embedded.js!<?php e(CACHE_BUST)?>"></script>
  </body>
</html>
