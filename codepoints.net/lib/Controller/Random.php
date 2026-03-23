<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Router\Redirect;


/**
 * select a (quasi-)random codepoint
 *
 * We add some weight to codepoints so that CJK and Hangul ones are more
 * seldom, because otherwise they would be selected over 90% of time. That's a
 * bit unfair for all non-CJK languages...
 */
final class Random extends Controller {

    #[\Override]
    public function __invoke($match, Array $env) : string {
        throw new Redirect(sprintf('/U+%04X', get_random_codepoint($env['db'])), 303);
        return '';
    }

}
