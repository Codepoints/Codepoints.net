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
class Random extends Controller {

    public function __invoke($match, Array $env) : string {
        /** @var Array{cp: string} */
        $data = $env['db']->getOne('
            SELECT cp
                FROM codepoints
                ORDER BY (
                    RAND() * (
                        CASE
                        WHEN
                            -- U+30000..U+3FFFF CJK in 4th plane
                            196608 <= cp AND cp <= 262143
                        THEN
                            3000000
                        WHEN
                            -- U+20000..U+2FFFF CJK in 3rd plane
                            131072 <= cp AND cp <= 196607
                        THEN
                            2000000
                        WHEN
                            -- U+3400..U+4DBF CJK in BMP
                            13312 <= cp AND cp <= 19903
                        THEN
                            1000000
                        WHEN
                            -- U+AC00..U+D7AF Hangul
                            44032 <= cp AND cp <= 55215
                        THEN
                            500000
                        ELSE
                            1
                        END
                    )
                ) ASC
                LIMIT 1');
        throw new Redirect(sprintf('U+%04X', $data['cp']), 303);
        return '';
    }

}
