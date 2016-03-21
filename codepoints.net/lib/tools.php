<?php


/**
 * convert array of codepoints to UTF-8 string
 * @see <http://php.net/manual/en/function.chr.php#88611>
 */
function unicode_to_utf8($str) {
    return array_reduce($str, function($utf8, $unicode) {
        return $utf8 . mb_convert_encoding('&#' . intval($unicode) . ';', 'UTF-8', 'HTML-ENTITIES');
    }, '');
} // unicode_to_utf8


/**
 * convert string to array of codepoints
 * @see http://php.net/manual/en/function.ord.php#109812
 */
function utf8_to_unicode($string) {
    $unicode = array();
    $offset = 0;
    while ($offset >= 0) {
        $code = ord(substr($string, $offset, 1));
        if ($code >= 128) {        //otherwise 0xxxxxxx
            if ($code < 224) {
                $bytesnumber = 2;                //110xxxxx
            } elseif ($code < 240) {
                $bytesnumber = 3;        //1110xxxx
            } elseif ($code < 248) {
                $bytesnumber = 4;    //11110xxx
            }
            $codetemp = $code - 192 - ($bytesnumber > 2 ? 32 : 0) - ($bytesnumber > 3 ? 16 : 0);
            for ($i = 2; $i <= $bytesnumber; $i++) {
                $offset ++;
                $code2 = ord(substr($string, $offset, 1)) - 128;        //10xxxxxx
                $codetemp = $codetemp*64 + $code2;
            }
            $code = $codetemp;
        }
        $offset += 1;
        if ($offset >= strlen($string)) {
            $offset = -1;
        }
        $unicode[] = $code;
    }
    return $unicode;
}


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
