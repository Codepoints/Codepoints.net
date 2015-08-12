<?php

/**
 * convert string to array of codepoints
 * @author Scott Reynen
 * @see <http://randomchaos.com/documents/?source=php_and_unicode>
 */
function utf8_to_unicode($str) {
    $unicode = array();
    $values = array();
    $lookingFor = 1;
    $lenstr = strlen($str);

    for ($i = 0; $i < $lenstr; $i++) {

        $thisValue = ord( $str[ $i ] );

        if ($thisValue < 128) {
            $unicode[] = $thisValue;
        } else {

            if (count($values) === 0) {
                $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
            }
            $values[] = $thisValue;

            if (count($values) === $lookingFor) {
                $number = ( $lookingFor === 3 ) ?
                    ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
                    ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );

                $unicode[] = $number;
                $values = array();
                $lookingFor = 1;
            }

        }

    }

    return $unicode;
} // utf8_to_unicode


/**
 * convert array of codepoints to UTF-8 string
 * @author Scott Reynen
 * @see <http://randomchaos.com/documents/?source=php_and_unicode>
 */
function unicode_to_utf8($str) {
    $utf8 = '';

    foreach ($str as $unicode) {
        if ($unicode < 128) {

            $utf8.= chr($unicode);

        } elseif ($unicode < 2048) {

            $utf8.= chr( 192 +  ( ( $unicode - ( $unicode % 64 ) ) / 64 ) );
            $utf8.= chr( 128 + ( $unicode % 64 ) );

        } else {

            $utf8.= chr( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) );
            $utf8.= chr( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) );
            $utf8.= chr( 128 + ( $unicode % 64 ) );

        }
    }

    return $utf8;
} // unicode_to_utf8


/**
 * test, if a string is a possible codepoint
 *
 * Note: We don't test, if this is *really* a codepoint, i.e., connect to
 * the database
 */
function maybeCodepoint($hexstring) {
    if (strlen($hexstring) > 6) {
        return false;
    }
    if (! ctype_xdigit($hexstring)) {
        return false;
    }
    return true;
}


/**
 * Get the scheme+host part of the request
 */
function get_origin() {
    $scheme = 'http';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] &&
        $_SERVER['HTTPS'] !== 'off') {
        $scheme .= 's';
    }

    if (isset($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
    } else {
        $host = $_SERVER['SERVER_NAME'];
    }
    $host = preg_replace('/[^a-zA-Z0-9.\-:\[\]]/', '', $host);

    return "$scheme://$host/";
}


/**
 * convert HSL to RGB colors
 */
function hsl2rgb($h, $s, $l) {
    $h_temp = $h / 360;
    $q = ($l < 0.5)? ($l * (1 + $s)) : ($l + $s - ($l*$s));
    $p = 2*$l - $q;
    $t_r = $h_temp + 1/3;
    $t_g = $h_temp;
    $t_b = $h_temp - 1/3;
    foreach (array("r", "g", "b") as $C) {
        if (${"t_$C"} < 0) {
            ${"t_$C"} += 1;
        } elseif (${"t_$C"} > 1) {
            ${"t_$C"} -= 1;
        }
        if (${"t_$C"} < 1/6) {
            $$C = $p+(($q-$p)*6*${"t_$C"});
        } elseif (${"t_$C"} >= 1/6 && ${"t_$C"} < 0.5) {
            $$C = $q;
        } elseif (${"t_$C"} >= 0.5 && ${"t_$C"} < 2/3) {
            $$C = $p+(($q-$p)*6*(2/3 - ${"t_$C"}));
        } else {
            $$C = $p;
        }
    }
    return array($r, $g, $b);
}


/**
 * HTML-quote a string or all elements of an array
 */
function q($s) {
    if (is_array($s)) {
        return array_map('q', $s);
    } elseif (is_string($s)) {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    } else {
        return $s;
    }
}


/**
 * shortcut for echo q()
 */
function e($s) {
    echo q($s);
}


/**
 * a continuous color generator for usage in the API
 *
 * @see http://krazydad.com/tutorials/makecolors.php for the maths
 */
function getNextColor($frequency1, $frequency2, $frequency3,
    $phase1, $phase2, $phase3, $center=null, $width=null) {
    static $i = 0;
    if (is_null($center)) {
        $center = 128;
    }
    if (is_null($width)) {
        $width = 127;
    }
    $red = min(255, max(0, round( sin($frequency1*$i + $phase1) * $width + $center)));
    $grn = min(255, max(0, round( sin($frequency2*$i + $phase2) * $width + $center)));
    $blu = min(255, max(0, round( sin($frequency3*$i + $phase3) * $width + $center)));
    $i += 1;
    return array($red, $grn, $blu);
}


#EOF
