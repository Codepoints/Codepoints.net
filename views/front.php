<?php $title = __('Find all Unicode characters from Hieroglyphs to Dingbats');
$hDescription = __('Codepoints is a site dedicated to Unicode and all things related to codepoints, characters, glyphs and internationalization.');
$canonical = '/';
include "header.php";
include "nav.php";
?>
<div class="payload front">
  <h1><?php e('Codepoints')?></h1>
  <form method="get" action="<?php e($router->getUrl('SearchResult'))?>" class="searchform">
    <p><input type="text" name="q" placeholder="<?php _e('Search a Character')?>"
       title="<?php _e('Enter a single character, like “丙” or “A”, or a term that describes the character, like “cyrillic” or “grass”')?>" />
       <button type="submit"><span><?php _e('search')?></span></button></p>
  </form>
  <article>
    <blockquote class="central">
      <p><?php echo __('<strong>Codepoint</strong>, <em>n.</em> the position of a character in
      an encoding system.')?></p>
    </blockquote>
    <p class="action">
      <span class="desc"><?php _e('Start here:')?> <small><?php _e('Browse one by one through blocks of characters')?></small></span>
      <a class="button browse" href="<?php e($router->getUrl('basic_latin'))?>"><?php _e('Browse Codepoints')?></a>
    </p>
    <p class="action">
      <span class="desc"><?php _e('Need help?')?> <small><?php _e('Answer questions to find matching characters')?></small></span>
      <a class="button find" href="<?php e($router->getUrl('wizard'))?>"><?php _e('Find My Codepoint')?></a>
    </p>
    <p class="action">
      <span class="desc"><?php _e('Expert Search!')?> <small><?php _e('Search for characters with particular properties')?></small></span>
      <a class="button expert" href="<?php e($router->getUrl('search'))?>"><?php _e('Search Codepoint')?></a>
    </p>
    <section class="bk">
      <h2><?php _e('About this Site')?></h2>
      <p><?php printf(__('Codepoints.net is dedicated to all the characters,
          that are defined in the %s. Theoretically, these should be
          <em>all characters ever used</em>. In practice Unicode has
          <em>%s codepoints</em> defined at the moment, mapping characters
          from %s to %s.'),
        '<a href="http://unicode.org">'.__('Unicode Standard').'</a>',
        $nCPs,
        '<a href="'.q($router->getUrl('egyptian_hieroglyphs')).'">'.__('Egyptian Hieroglyphs').'</a>',
        '<a href="'.q($router->getUrl('dingbats')).'">'.__('Dingbats and Symbols').'</a>'
       )?>
      </p>
      <p><?php printf(__('All codepoints are arranged in 17 so-called
       %s. These planes are further divided into several blocks with
       %s being the first one. You can browse one by one by starting with
       the first codepoint, %s or %s for a specific character. If you’re
       not fully sure, try %s, to narrow down the candidates. Or maybe you
       are more daring and want %s?'),
        '<a href="'.q($router->getUrl('planes')).'">'.__('planes').'</a>',
        '<a href="'.q($router->getUrl('/basic_latin')).'">'.__('Basic Latin').'</a>',
        _cp(Codepoint::getCP(0, $router->getSetting('db'))),
        '<a href="'.q($router->getUrl('search')).'">'.__('search').'</a>',
        '<a href="'.q($router->getUrl('wizard')).'">'.__('“Find My Codepoint”').'</a>',
        '<a href="'.q($router->getUrl('random')).'">'.__('a random codepoint').'</a>'
      )?>
      </p>
    </section>
<!--
    <section class="bk">
      <h2>The <i>(currently defined)</i> Unicode Planes</h2>
      <ol>
        <?php foreach ($planes as $plane):?>
          <li><a href="<?php e($router->getUrl($plane))?>"><?php e($plane->name)?></a></li>
        <?php endforeach?>
      </ol>
    </section>
-->
    <?php if ($daily):
    $codepoint = $daily[0];
    $props = $codepoint->getProperties();
    $block = $codepoint->getBlock();
    $s = function($cat) use ($router, $info, $props) {
        echo '<a href="';
        e($router->getUrl('search?'.$cat.'='.$props[$cat]));
        echo '">';
        e($info->getLabel($cat, $props[$cat]));
        echo '</a>';
    };
    ?>
      <section class="bk">
        <aside class="other">
          <h2><?php _e('Codepoints of the Day')?></h2>
          <div id="ucotd_cal" data-date="<?php e(date('Y-m-d'))?>"></div>
        </aside>
        <h2><?php _e('Codepoint of the Day:')?>
            <a href="<?php e($router->getUrl($codepoint))?>">U+<?php e($codepoint->getId('hex'))?> <?php e($codepoint->getName())?></a></h2>
        <figure>
          <a href="<?php e($router->getUrl($codepoint))?>"><span class="fig"><?php e($codepoint->getSafeChar())?></span></a>
        </figure>
        <div class="abstract">
          <p>
<?php 
    $plane = $codepoint->getPlane();
    printf(__('U+%04X was added to Unicode in version
            %s. It belongs to the block %s in the %s.'),
            $codepoint->getId(),
            '<a href="'.q($router->getUrl('search?age='.$props['age'])).'">'.q($info->getLabel('age', $props['age'])).'</a>',
            _bl($block),
            '<a class="pl" href="'.q($router->getUrl($plane)).'">'.q($plane->name).'</a>'
        );
    if ($props['Dep']):
        printf(__('This codepoint is %s.'),
            '<a href="'.q($router->getUrl('search?Dep=1')).'">'.__('deprecated').'</a>');
    endif;
?>
          </p>
          <p>
            <?php printf(__('This character is a %s and is %s…'),
                '<a href="'.q($router->getUrl('search?gc='.$props['gc'])).'">'.q($info->getLabel('gc', $props['gc'])).'</a>',
                ($props['sc'] === 'Zyyy')? sprintf(__('%scommonly%s used'), '<a href="'.q($router->getUrl('search?sc='.$props['sc'])).'">', '</a>') :
                sprintf(__('mainly used in the %s script'), '<a href="'.q($router->getUrl('search?sc='.$props['sc'])).'">'.q($info->getLabel('sc', $props['sc'])).'</a>')
            )?>
          </p>
          <p><strong><a href="<?php e($router->getUrl($codepoint))?>"><?php _e('» View full description of this codepoint.')?></a></strong></p>
        </div>
      </section>
    <?php endif?>
    <section class="bk blog-preview">
    </section>
  </article>
</div>
<?php
$footer_scripts = array("/static/js/dailycp.js");
include "footer.php"?>
