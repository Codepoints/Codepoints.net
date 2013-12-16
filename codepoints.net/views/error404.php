<?php
$title = __('Page not Found');
if (! is_null($int)) {
    $title = __('Codepoint not Found');
}
$hDescription = __('HTTP error 404: This page doesn’t exist.');
include "header.php";
$nav = array();
if ($prev) {
    $nav['prev'] = _cp($prev, 'prev', 'min', 'span');
} elseif ($plane && $plane->getPrev()) {
    $nav["prev"] = '<a rel="prev" href="'.q($router->getUrl($plane->getPrev())).'">'.q($plane->getPrev()->name).'</a>';
}
if ($block) {
    $nav["up"] = _bl($block, 'up', 'min', 'span');
} elseif ($plane) {
    $nav["up"] = '<a class="pl" rel="up" href="'.q($router->getUrl($plane)).'">'.q($plane->getName()).'</a>';
} else {
    $nav["up"] = '<a rel="up" href="'.q($router->getUrl('planes')).'">Unicode</a>';
}
if ($next) {
    $nav['next'] = _cp($next, 'next', 'min', 'span');
} elseif ($plane && $plane->getNext()) {
    $nav["next"] = '<a rel="next" href="'.q($router->getUrl($plane->getNext())).'">'.q($plane->getNext()->name).'</a>';
}
include "nav.php";
?>
<div class="payload codepoint error">
  <?php if (is_null($int)):?>
    <h1><?php e($title)?></h1>
  <?php else:?>
    <figure>
      <span class="fig">&#xFFFD;</span>
    </figure>
    <h1 class="quiet"><?php printf('U+%04X', $int)?> CECI N’EST PAS UNICODE</h1>
    <section class="abstract">
      <p><?php _e('This codepoint doesn’t exist.')?>
      If it would, it’d be located in the
      <?php if($plane):?>
        <a href="<?php e($router->getUrl($plane))?>"><?php e($plane->getName())?></a>.
      <?php elseif ($int > 0x10FFFF):?>
        Nirvana of Undefined Behaviour beyond the 17<sup>th</sup> plane, a land <a href="http://www.unicode.org/mail-arch/unicode-ml/y2003-m10/0234.html">no member of the Unicode mailing list has ever seen</a>.
      <?php else:?>
        <?php e(ceil(round($int / 0xFFFF, 2)))?><sup>th</sup> plane.
      <?php endif?>
      <?php if ($block):?>
        You can find surrounding codepoints in the block
        <?php bl($block, '', 'min')?>.
      <?php endif?>
      </p>
    </section>
    <section>
      <table class="props representations">
        <thead>
          <tr>
            <th><?php _e('System')?></th>
            <th><?php _e('Representation')?></th>
          </tr>
        </thead>
        <tbody>
          <tr class="primary">
            <th><?php _e('Nº')?></th>
            <td class="repr-number"><?php e($int)?></td>
          </tr>
          <tr class="primary">
            <th><?php _e('UTF-8')?></th>
            <td class="repr-number"><?php e(
                join(' ',
                    str_split(
                        strtoupper(
                            bin2hex(
                                mb_convert_encoding('&#'.$int.';', 'UTF-8', 'HTML-ENTITIES')
                            )
                        )
                    , 2)
                )
            )?></td>
          </tr>
          <tr class="primary">
            <th><?php _e('UTF-16')?></th>
            <td class="repr-number"><?php e(
                join(' ',
                    str_split(
                        strtoupper(
                            bin2hex(
                                mb_convert_encoding('&#'.$int.';', 'UTF-16', 'HTML-ENTITIES')
                            )
                        )
                    , 2)
                )
            )?></td>
          </tr>
          <tr>
            <th><?php _e('UTF-32')?></th>
            <td class="repr-number"><?php e(
                join(' ',
                    str_split(
                        strtoupper(
                            bin2hex(
                                mb_convert_encoding('&#'.$int.';', 'UTF-32', 'HTML-ENTITIES')
                            )
                        )
                    , 2)
                )
            )?></td>
          </tr>
          <tr>
            <th><?php _e('URL-Quoted')?></th>
            <td class="repr-number">%<?php e(
                join('%',
                    str_split(
                        strtoupper(
                            bin2hex(
                                mb_convert_encoding('&#'.$int.';', 'UTF-8', 'HTML-ENTITIES')
                            )
                        )
                    , 2)
                )
            )?></td>
          </tr>
        </tbody>
      </table>
    </section>
  <?php endif?>
  <?php if (count($cps)):?>
    <ul class="data">
      <?php foreach($cps as $cp):?>
      <li><?php cp($cp)?></li>
      <?php endforeach?>
    </ul>
  <?php endif?>
  <p><?php _e('Search other codepoints:')?></p>
  <?php $searchprefix = 'err_'; include "quicksearch.php"; unset($searchprefix); ?>
</div>
<?php include "footer.php"?>
