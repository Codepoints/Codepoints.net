<?php
$db = $router->getSetting('db');

$title = sprintf('U+%04X PRIVATE USE CHARACTER', $codepoint);
$prev = Codepoint::getCp($codepoint <= 0xF8FF? 0xD7FB : 0xE01EF, $db);
$next = $codepoint <= 0xF8FF? Codepoint::getCp(0xF900, $db) : null;
if ($codepoint <= 0xF8FF) {
    $block = new UnicodeBlock('Private Use Area', $db);
} elseif ($codepoint <= 0xFFFFF) {
    $block = new UnicodeBlock('Supplementary Private Use Area-A', $db);
} else {
    $block = new UnicodeBlock('Supplementary Private Use Area-B', $db);
}
$headdata = sprintf('<link rel="up" href="%s"/>', q($router->getUrl($block)));
if ($prev):
    $headdata .= '<link rel="prev" href="' . q($router->getUrl($prev)) . '" />';
endif;
if ($next):
    $headdata .= '<link rel="next" href="' . q($router->getUrl($next)) . '" />';
endif;
$hDescription = sprintf(__('U+%04X is a Unicode codepoint in the block “%s”. It is a so-called “private use” codepoint, deliberately not assigned to any character.'),
    $codepoint, $block->getName());
$canonical = $router->getUrl(sprintf('U+%04X', $codepoint));
$headdata .= sprintf('<meta name="twitter:site" content="@codepointsnet"/>
<meta name="twitter:url" content="https://codepoints.net%s"/>
<meta name="twitter:title" content="%s"/>
<meta name="twitter:description" content="%s"/>',
q($canonical), q($title), q($hDescription));
include "header.php";
$nav = array();
if ($prev) {
    $nav['prev'] = _cp($prev, 'prev', 'min', 'span');
}
$nav["up"] = _bl($block, 'up', 'min', 'span');
if ($next) {
    $nav['next'] = _cp($next, 'next', 'min', 'span');
}
include "nav.php";

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

$repr = function($coding='UTF-8') use ($codepoint) {
    return join(' ',
        str_split(
            strtoupper(
                bin2hex(
                    mb_convert_encoding('&#'.$codepoint.';', $coding, 'HTML-ENTITIES')
                )
            )
        , 2)
    );
};

$s = function($cat) use ($router, $info, $props) {
    echo '<a href="';
    e($router->getUrl('search?'.$cat.'='.rawurlencode($props[$cat])));
    echo '">';
    e($info->getLabel($cat, $props[$cat]));
    echo '</a>';
};
?>
<div class="payload codepoint">
  <figure>
    <span class="fig">&#xFFFD;</span>
  </figure>
  <aside>
    <!--h3>Properties</h3-->
    <dl>
      <dt><?php _e('Nº')?></dt>
      <dd><?php printf('U+%04X', $codepoint)?></dd>
      <dt><?php _e('UTF-8')?></dt>
      <dd><?php e($repr())?></dd>
      <?php foreach(array('gc', 'sc', 'bc', 'dt', 'ea') as $cat):?>
        <dt><?php e($info->getCategory($cat))?></dt>
        <dd><a href="<?php e('search?'.$cat.'='.$props[$cat])?>"><?php e($info->getLabel($cat, $props[$cat]))?></a></dd>
      <?php endforeach?>
    </dl>
  </aside>
  <h1><?php printf('U+%04X', $codepoint)?> PRIVATE USE CODEPOINT</h1>
  <section class="abstract">
    <p>
<?php printf(__('This is a Private Use codepoint. That is, it is deliberately not assigned to any character. It was added to Unicode in version %s and belongs to the block %s.'),
    '<a href="'.q($router->getUrl('search?age='.$props['age'])).'">'.$props['age'].'</a>',
    '<a href="'.q($router->getUrl($block)).'">'.$block.'</a>');
?>
    </p>
    <p>
<?php
    printf(__('The glyph is %snot a composition%s.'),
        '<a href="'.q($router->getUrl('search?dt=none')).'">',
        '</a>');

    echo ' ';
    printf(__('It has a %s %s.'),
        '<a href="'.q($router->getUrl('search?ea='.$props['ea'])).'">'.
        q($info->getLabel('ea', $props['ea'])).'</a>',
        q($info->getCategory('ea')));

    echo ' ';
    printf(__('In bidirectional context it acts as %s and is %snot mirrored%s.'),
        '<a href="'.q($router->getUrl('search?bc='.$props['bc'])).'">'.
        q($info->getLabel('bc', $props['bc'])).'</a>',
        '<a href="'.q($router->getUrl('search?bc='.$props['bc'].'&bm='.
        (int)$props['Bidi_M'])).'">',
        '</a>'
    );

    echo ' ';
    printf(__('In text U+%04X behaves as %s regarding line breaks. It has
        type %s for sentence and %s for word breaks. The %s is %s.'),
        $codepoint,
        '<a href="'.q($router->getUrl('search?lb='.$props['lb'])).'">'.
        q($info->getLabel('lb', $props['lb'])).'</a>',
        '<a href="'.q($router->getUrl('search?SB='.$props['SB'])).'">'.
        q($info->getLabel('SB', $props['SB'])).'</a>',
        '<a href="'.q($router->getUrl('search?WB='.$props['WB'])).'">'.
        q($info->getLabel('WB', $props['WB'])).'</a>',
            q($info->getCategory('GCB')),
        '<a href="'.q($router->getUrl('search?GCB='.$props['GCB'])).'">'.
        q($info->getLabel('GCB', $props['GCB'])).'</a>');
?>
    </p>
    <p><?php printf(__('The %sWikipedia%s has the following information about Private Use codepoints:'), '<a href="http://en.wikipedia.org/wiki/Private_Use_Areas">', '</a>')?></p>
    <blockquote cite="http://en.wikipedia.org/wiki/Private_Use_Areas">
      <p>In Unicode, the <b>Private Use Areas (PUA)</b> are three ranges of code points (<code>U+E000</code>–<code>U+F8FF</code> in the BMP, and in planes 15 and 16) that, by definition, will not be assigned characters by the Unicode Consortium. The code points in these areas can not be considered as standardized characters in Unicode itself. They are intentionally left undefined so that third parties may define their own characters without conflicting with Unicode Consortium assignments. Under the Unicode Stability Policy, the Private Use Areas will remain allocated for that purpose in all future Unicode versions.</p>
      <p>Assignments to Private Use Area characters need not be "private" in the sense of strictly internal to an organisation; a number of assignment schemes have been published by several organisations. Such publication may include a font that supports the definition (showing the glyphs), and software making use of the private-use characters (e.g. a graphics character for a "print document" function). By definition, multiple private parties may assign different characters to the same code point, with the consequence that a user may see one private character from an installed font where a different one was intended.</p>
    </blockquote>
  </section>
  <section>
    <h2><?php _e('Representations')?></h2>
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
          <td class="repr-number"><?php e($codepoint)?></td>
        </tr>
        <tr class="primary">
          <th><?php _e('UTF-8')?></th>
          <td><?php e($repr('UTF-8'))?></td>
        </tr>
        <tr class="primary">
          <th><?php _e('UTF-16')?></th>
          <td><?php e($repr('UTF-16'))?></td>
        </tr>
        <tr>
          <th><?php _e('UTF-32')?></th>
          <td><?php e($repr('UTF-32'))?></td>
        </tr>
        <tr>
          <th><?php _e('URL-Quoted')?></th>
          <td>%<?php e($repr('UTF-8', '%'))?></td>
        </tr>
        <tr>
          <th><?php _e('HTML-Escape')?></th>
          <td>&amp;#x<?php printf('%04X', $codepoint)?>;</td>
        </tr>
      </tbody>
    </table>
  </section>
  <section>
    <h2><?php _e('Complete Record')?></h2>
    <?php include "codepoint/props.php"?>
  </section>
  </div>
<?php include "footer.php"?>
