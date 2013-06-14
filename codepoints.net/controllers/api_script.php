<?php

$router->registerAction(function ($url, $o) {
    // Script description: script/Xxxx
    if (preg_match('#^api/script/(?:[A-Z][a-z]{3})(?:%20[A-Z][a-z]{3})*$#', $url, $m)) {
        return True;
    }
    return False;
}, function($request, $o) {
    header('Content-Type: application/json; charset=UTF-8');
    $trunk = rawurldecode(substr($request->trunkUrl, 11));
    $j = array();
    $found = False;
    $stm = $o['db']->prepare('SELECT abstract, src
                                FROM script_abstract WHERE sc = :sc');
    foreach (explode(' ', $trunk) as $sc) {
        $stm->execute(array('sc'=>$sc));
        $r = $stm->fetch(PDO::FETCH_ASSOC);
        if ($r['abstract']) {
            $j[$sc] = array(
                'name' => $o['info']->getLabel('sc', $sc),
                'abstract' => strip_tags($r['abstract'], '<p><b><strong class="selflink"><strong><em><i><var><sup><sub><tt><ul><ol><li><samp><small><hr><h2><h3><h4><h5><dfn><dl><dd><dt><u><abbr><big><blockquote><br><center><del><ins><kbd>'),
                'src' => $r['src'],
            );
            $found = true;
        } else {
            $j[$sc] = Null;
        }
    }
    if (! $found) {
        header('HTTP/1.0 404 Not Found');
    }
    echo json_encode($j);
});

//__END__
