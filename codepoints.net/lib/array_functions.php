<?php


/**
 *
 * @param array-key $key
 * @param mixed $default
 * @return mixed
 */
function array_get(Array $array, $key, $default=null) {
    if (array_key_exists($key, $array)) {
        return $array[$key];
    }
    return $default;
}
