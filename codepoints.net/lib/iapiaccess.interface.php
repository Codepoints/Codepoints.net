<?php


define('API_PRECONDITION_FAILED', 2);
define('API_REQUEST_TOO_LONG', 4);
define('API_NOT_FOUND', 8);
define('API_BAD_REQUEST', 16);


/**
 * describes the public interface for versions of the API
 */
interface iAPIAccess {

    public function __construct($action, $request, $db);

    public function finish();

    public function run($data = null);

    public function handleError();

}


#EOF
