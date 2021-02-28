<?php

/**
 * get the currently requested page for pagination
 *
 * Pages are 1-based.
 */
function get_page() : int {
    $page = (int)filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
    if (! $page) {
        $page = 1;
    }
    return $page;
}
