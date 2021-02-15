<?php

/**
 * check, if a code point is in a private use area
 */
function is_pua(int $cp) : bool {
    return ((0xE000 <= $cp && $cp <= 0xF8FF) ||
            (0xF0000 <= $cp && $cp <= 0xFFFFD) ||
            (0x100000 <= $cp && $cp <= 0x10FFFD));
}
