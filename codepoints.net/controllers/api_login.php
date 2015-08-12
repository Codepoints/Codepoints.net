<?php

$router->registerAction('api/login', function ($request, $o) {
    // BrowserID login
    header('Content-Type: application/json');

    if (! isset($_GET['assertation'])) {
        die('{"status":"error","message":"Missing parameter"}');
    }

    $ch = curl_init();
    $data= array('assertation' => $_GET['assertation'],
                 'audience' => 'https://codepoints.net');
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, 'https://browserid.org/verify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);
    curl_close($ch);
    $state = json_decode($result);

    if ($state === null || ! array_key_exists('status', $state)) {
        die('{"status":"error","message":"Couldn\'t verify assertation"}');
    } elseif ($state['status'] !== 'okay') {
        die('{"status":"error","message":"Assertation wrong"}');
    } else {
        echo '{"status":"okay"}';
    }
});

//__END__
