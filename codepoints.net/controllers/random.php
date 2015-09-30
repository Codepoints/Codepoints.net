<?php

$router->registerAction('random', function ($request, $o) {
    // random codepoint
    $x = $o['db']->prepare('
        SELECT cp
            FROM codepoints
            ORDER BY (
                ABS(RAND())/10000000000000 * (
                    CASE
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
    $x->execute();
    $row = $x->fetch();
    $router = Router::getRouter();
    $router->redirect(sprintf('U+%04X', $row['cp']));
});

//__END__
