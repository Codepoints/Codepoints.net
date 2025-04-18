<?php

use \Analog\Analog;
use Codepoints\Unicode\Codepoint;
use Codepoints\Unicode\Block;
use Codepoints\Unicode\Plane;
use Codepoints\Router\AssetHandler;


/**
 * translate a string, the quick way
 */
function __(string $original): string {
    global $translator;
    return $translator? $translator->translate($original) : $original;
}

/**
 * HTML-quote a string
 */
function q(string $s) : string {
    return str_replace(
        ["\0"],
        ['&#x0;'],
        htmlspecialchars($s, ENT_QUOTES, 'UTF-8'));
}

/**
 * shortcut for "q(__())"
 */
function _q(string $s) : string {
    return q(__($s));
}

/**
 * return gettext-translated MessageFormat patterns
 */
function _f(string $pattern, Array $values) : string {
    global $lang;
    $formatter = new MessageFormatter($lang, __($pattern));
    $result = $formatter->format($values);
    if ($result === false) {
        Analog::log('MessageFormat error: '.$formatter->getErrorMessage()."\n".'for pattern '.$pattern);
    }
    return $result;
}

/**
 * return the representation for a code point link
 */
function cp(Codepoint|Array $codepoint, string $rel='', string $class='', ?string $alt_title=null) : string {
    if (is_array($codepoint)) {
        $r = '';
        foreach ($codepoint as $cp) {
            $r .= cp($cp, $rel, $class);
        }
        return $r;
    }
    if ($rel) {
        $rel = ' rel="'.q($rel).'"';
    }
    if ($class) {
        $class = ' '.q($class);
    }
    return sprintf('<a class="ln cp%s"%s href="%s" data-cp="%s" data-sensitivity="%s">'.
            '%s <span class="title">%s</span>'.
        '</a>',
        $class,
        $rel,
        q(url($codepoint)),
        q($alt_title ?? (string)$codepoint),
        q((string)$codepoint->sensitivity->value),
        cpimg($codepoint),
        q(case_cp_name($codepoint->name)));
}

/**
 * return the image element for a code point
 *
 * When https://bugzilla.mozilla.org/show_bug.cgi?id=1027106 is fixed, we
 * can switch back to good ol' <img> again.
 */
function cpimg(Codepoint $codepoint, int $width=16, ?array $src=null) : string {
    $image_generator = $codepoint->image;
    return $image_generator($width, $src);
}

/**
 * return the representation for a block link
 */
function bl(Block $block, string $rel='', string $class='') : string {
    if ($rel) {
        $rel = ' rel="'.q($rel).'"';
    }
    if ($class) {
        $class = ' '.q($class);
    }
    return sprintf(
        '<a class="ln bl%s"%s href="%s">'.
            '<span class="meta">%s</span> '.
            '%s <span class="title">%s</span>'.
        '</a>',
        $class, $rel, q(url($block)),
        sprintf(__('U+%04X to U+%04X'), $block->first, $block->last),
        blimg($block), q($block->name));
}

/**
 * return the image element for a block
 */
function blimg(Block $block, int $width=16) : string {
    static $block_to_last_resort_map = [
        'basiclatin'                                  => 'latin',
        'latin1supplement'                            => 'latin1',
        'spacingmodifierletters'                      => 'spacingmodifiers',
        'combiningdiacriticalmarks'                   => 'combiningdiacritics',
        'greekandcoptic'                              => 'greek',
        'arabicsupplement'                            => 'arabicsup',
        'ethiopicsupplement'                          => 'ethiopicsup',
        'unifiedcanadianaboriginalsyllabics'          => 'canadiansyllabics',
        'unifiedcanadianaboriginalsyllabicsext'       => 'ucasext',
        'combiningdiacriticalmarksext'                => 'cmbdiacriticalmarksext',
        'sundanesesupplement'                         => 'sundanesesuppl',
        'vedicextensions'                             => 'vedicext',
        'phoneticextensions'                          => 'phoneticext',
        'phoneticextensionssupplement'                => 'phoneticextsup',
        'combiningdiacriticalmarkssupplement'         => 'combiningdiacriticssup',
        'latinadditional'                             => 'latinext',
        'superscriptsandsubscripts'                   => 'supersubscripts',
        'currencysymbols'                             => 'currency',
        'combiningdiacriticalmarksforsymbols'         => 'combiningsymbols',
        'mathematicaloperators'                       => 'mathoperators',
        'miscellaneoustechnical'                      => 'misctechnical',
        'opticalcharacterrecognition'                 => 'opticalrecognition',
        'enclosedalphanumerics'                       => 'enclosedalphanum',
        'miscellaneoussymbols'                        => 'miscsymbols',
        'miscellaneousmathematicalsymbolsa'           => 'miscmathsymbolsa',
        'supplementalarrowsa'                         => 'arrowssupplementa',
        'braillepatterns'                             => 'braillesymbols',
        'supplementalarrowsb'                         => 'arrowssupplementb',
        'miscellaneousmathematicalsymbolsb'           => 'miscmathsymbolsb',
        'supplementalmathematicaloperators'           => 'mathoperatorssup',
        'miscellaneoussymbolsandarrows'               => 'miscsymbolsarrows',
        'georgiansupplement'                          => 'georgiansup',
        'supplementalpunctuation'                     => 'punctuationsup',
        'cjkradicalssupplement'                       => 'cjkradicalssup',
        'ideographicdescriptioncharacters'            => 'ideographicdescript',
        'cjksymbolsandpunctuation'                    => 'cjksymbolspunct',
        'hangulcompatibilityjamo'                     => 'compatibilityjamo',
        'katakanaphoneticextensions'                  => 'katakanaext',
        'enclosedcjklettersandmonths'                 => 'enclosedcjk',
        'yijinghexagramsymbols'                       => 'yijinghexagram',
        'cjkunifiedideographs'                        => 'cjkideograph',
        'yisyllables'                                 => 'yi',
        'modifiertoneletters'                         => 'modifiertones',
        'commonindicnumberforms'                      => 'commonindicnumforms',
        'meeteimayekextensions'                       => 'meeteimayekext',
        'highsurrogates'                              => 'highsurrogate',
        'highprivateusesurrogates'                    => 'highprivatesurrogate',
        'lowsurrogates'                               => 'lowsurrogate',
        'privateusearea'                              => 'privateuse',
        'cjkcompatibilityideographs'                  => 'cjkideographcompat',
        'alphabeticpresentationforms'                 => 'alphapresentation',
        'arabicpresentationformsa'                    => 'arabicpresentationa',
        'combininghalfmarks'                          => 'combininghalfforms',
        'cjkcompatibilityforms'                       => 'cjkcompatibilityform',
        'smallformvariants'                           => 'smallforms',
        'arabicpresentationformsb'                    => 'arabicpresentationb',
        'halfwidthandfullwidthforms'                  => 'halffullwidthforms',
        'ancientgreeknumbers'                         => 'greeknumbers',
        'cypriotsyllabary'                            => 'cypriot',
        'inscriptionalparthian'                       => 'inscrparthian',
        'inscriptionalpahlavi'                        => 'inscrpahlavi',
        'ruminumeralsymbols'                          => 'ruminumeralsym',
        'unifiedcanadianaboriginalsyllabicsexta'      => 'ucasexta',
        'cuneiformnumbersandpunctuation'              => 'cuneiformnumbers',
        'egyptianhieroglyphformatcontrols'            => 'egypthiercontrols',
        'bamumsupplement'                             => 'bamumsuppl',
        'kanasupplement'                              => 'kanasuppl',
        'shorthandformatcontrols'                     => 'shorthandformatcntrls',
        'symbolsforlegacycomputingsupplement'         => 'legacycomputingsymbsupp',
        'znamennymusicalnotation'                     => 'znamennymusic',
        'byzantinemusicalsymbols'                     => 'byzantinemusic',
        'ancientgreekmusicalnotation'                 => 'greekmusic',
        'taixuanjingsymbols'                          => 'taixuanjing',
        'countingrodnumerals'                         => 'countingrods',
        'mathematicalalphanumericsymbols'             => 'alphamathsymbols',
        'arabicmathematicalalphabeticsymbols'         => 'arabicmathalphasym',
        'enclosedalphanumericsupplement'              => 'enclalphasuppl',
        'enclosedideographicsupplement'               => 'enclideographicsuppl',
        'miscellaneoussymbolsandpictographs'          => 'miscsymandpict',
        'transportandmapsymbols'                      => 'transportandmapsym',
        'alchemicalsymbols'                           => 'alchemicalsym',
        'supplementalsymbolsandpictographs'           => 'supplementalsymbpict',
        'symbolsandpictographsexta'                   => 'symbpictexta',
        'symbolsforlegacycomputing'                   => 'legacycomputingsymb',
        'cjkcompatibilityideographssupplement'        => 'cjkideocompatsup',
        'supplementaryprivateuseareaa'                => 'privateplane15',
        'supplementaryprivateuseareab'                => 'privateplane16',
    ];
    $name = str_replace(['latin extended', 'extended', 'cjk unified ideographs extension', ' ', '_', '-'], ['latin', 'ext', 'cjkext'], strtolower($block->name));
    if (array_key_exists($name, $block_to_last_resort_map)) {
        $name = $block_to_last_resort_map[$name];
    }
    $url = sprintf('%s#%s', static_url('src/images/LastResort.svg'), $name);
    return sprintf('<svg width="%s" height="%s"><svg viewBox="194 97 1960 1960" width="100%%" height="100%%">'.
        '<use xlink:href="%s"/></svg></svg>', $width, $width, $url);
}

/**
 * return the representation for a plane link
 */
function pl(Plane $plane, string $rel='', string $class='') : string {
    if ($rel) {
        $rel = ' rel="'.q($rel).'"';
    }
    if ($class) {
        $class = ' '.q($class);
    }
    return sprintf(
        '<a class="ln pl%s"%s href="%s">'.
            '<span class="meta">%s</span> '.
            '%s <span class="title">%s</span>'.
        '</a>',
        $class, $rel, q(url($plane)),
        sprintf(__('U+%04X to U+%04X'), $plane->first, $plane->last),
        plimg($plane), q($plane->name));
}

/**
 * return the image element for a plane
 */
function plimg(Plane $plane, int $width=16) : string {
    $map = [
        'notdefplanezero',
        'notdefplaneone',
        'notdefplanetwo',
        'notdefplanethree',
        'notdefplanefour',
        'notdefplanefive',
        'notdefplanesix',
        'notdefplaneseven',
        'notdefplaneeight',
        'notdefplanenine',
        'notdefplaneten',
        'notdefplaneeleven',
        'notdefplanetwelve',
        'notdefplanethirteen',
        'notdefplanefourteen',
        'privateplane15',
        'privateplane16',
    ];
    $name = $map[$plane->first / 0x10000];
    $url = sprintf('%s#%s', static_url('src/images/LastResort.svg'), $name);
    return sprintf('<svg width="%s" height="%s"><svg viewBox="194 97 1960 1960" width="100%%" height="100%%">'.
        '<use xlink:href="%s"/></svg></svg>', $width, $width, $url);
}

/**
 * generate an URL from any item (Codepoint, Block, ...)
 *
 * @param Codepoint|Block|Plane|string $item
 * @param boolean $absolute whether to make the URL absolute
 */
function url($item, $absolute=false) : string {
    $path = $absolute? 'https://codepoints.net/' : '/';
    if ($item instanceof Codepoint) {
        return $path.sprintf('U+%04X', $item->id);
    }
    if ($item instanceof Block) {
        return $path.rawurlencode(str_replace(' ', '_', strtolower($item->name)));
    }
    if ($item instanceof Plane) {
        $base = str_replace(' ', '_', strtolower($item->name));
        if (substr($base, 0, 6) !== 'plane_' && substr($base, -6) !== '_plane') {
            $base .= '_plane';
        }
        return $path.rawurlencode($base);
    }
    return $path.ltrim(str_replace([
        '%26', '%2B', '%2F', '%3D', '%3F',
    ], [
        '&', '+', '/', '=', '?',
    ], rawurlencode($item)), '/');
}

/**
 * convert all-uppercase names to a more readable case
 */
function case_cp_name(string $name) : string {
    $name = ucwords(strtolower($name), ' ()-');
    $name = preg_replace_callback('/(Dvd\\b|Nk(?=o)|Ipa\\b|Cjk|Dna\\b|Lf\\b|Ff\\b|Cr\\b|Nel\\b|(?<=Ideograph-|U\\+)[0-9a-f]+)/', function(Array $match) : string {
        return strtoupper($match[0]);
    }, $name);
    $name = str_replace([
        'Oclock', 'Of ', 'With ', 'And ', 'Ok ', 'For ', 'Ac Current', 'Fleur-De-Lis',
    ], [
        'O’Clock', 'of ', 'with ', 'and ', 'OK ', 'for ', 'AC Current', 'Fleur-de-lis',
    ], $name);
    return $name;
}

/**
 * get an asset URL
 */
function static_url(string $path): string {
    static $assethandler;
    if (! $assethandler) {
        $assethandler = new AssetHandler();
    }
    return $assethandler->getUrl($path);
}

/**
 * format a code point name for display
 */
function format_codepoint_name(string $name): string {
  return str_replace(
      ['Ideograph-', '*'],
      ["Ideo\u{00AD}graph-\u{200B}", ' <span tabindex="0" aria-description="'.q('This codepoint has no canonical name. The shown name is an alias defined by Unicode.').'" class="no-na tooltip">*</span>'],
      q(case_cp_name($name)));
}

/**
 * get the site origin
 */
function get_origin(): string {
    $scheme = 'http';
    if (filter_input(INPUT_SERVER, 'HTTPS') === 'on') {
        $scheme .= 's';
    }
    $host = filter_input(INPUT_SERVER, 'HTTP_HOST') ?? 'codepoints.net';
    return sprintf('%s://%s/', $scheme, $host);
}
