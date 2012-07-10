<?php

$router->registerAction(function ($url, $o) {
    // Possible codepoint name, like "LATIN CAPITAL LETTER A"
    $c = rawurldecode($url);
    if (preg_match('/^[A-Z][A-Z0-9_ -]{1,127}$/', $c)) {
        // shortest: "OX", longest has 83 chars (Unicode 6.1)
        $cp = False;
        try {
            $cp = Codepoint::getByName($c, $o['db']);
        } catch (Exception $e) {
            return False;
        }
        return $cp;
    }
    return False;
}, function($request) {
    $router = Router::getRouter();
    $router->redirect(sprintf('U+%04X', $request->data->getId()));
});

//__END__
