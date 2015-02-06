<?php


require_once __DIR__.'/tools.php';


/**
 * the handler for the Great Codepoints API V1
 */
class API_v1 implements iAPIAccess {

    /**
     * the requested API action
     */
    protected $_action;

    /**
     * the original request object
     * */
    protected $_request;

    /**
     * the response from calling a certain action
     */
    protected $_response;

    /**
     * the database connection
     */
    protected $_db;

    /**
     * the last occuring error, if any
     */
    protected $_error;

    /**
     * the MIME type of the response
     */
    protected $_mime = 'application/json';

    /**
     * Last-Modified based on database mtime and api action file mtime
     */
    protected $_mtime;

    /**
     * create a new API response
     */
    public function __construct($action, $request, $db) {
        $this->_action = $action;
        $this->_request = $request;
        $this->_db = $db;
    }

    /**
     * finish the API response, print headers and content
     */
    public function finish() {
        if (! $this->_error) {
            $this->_finish($this->_response);
            flush();

            // track page views without ourself as referrer
            if (! isset($_SERVER['HTTP_REFERER']) ||
                ! $_SERVER['HTTP_REFERER'] ||
                preg_replace('/^https?:\/\/([^\/]*)(\/.*)?$/', '$1',
                    $_SERVER['HTTP_REFERER']) !== 'codepoints.net') {
                PiwikTracker::$URL = 'http://stats.codepoints.net/';
                $piwikTracker = new PiwikTracker(4);
                $piwikTracker->setCustomVariable( 1, 'mode', 'api', 'page' );
                $piwikTracker->doTrackPageView('API: '.$this->_action);
            }
        }
    }

    /**
     * generate an API error to forcefully jump to $this->handleError
     */
    public function throwError($code, $message, $data=null) {
        $this->_error = array($code, $message, $data);
        throw new APIException($message);
    }

    /**
     * run the API action and collect response and errors
     */
    public function run($data = null) {
        if ($this->_request->method === "DELETE") {
            $this->throwError(API_BAD_REQUEST,
                              _("Ye?h, th?nks! You er?sed this codepoint. Are you h?ppy now?"));
        }
        if ($this->_request->method === "PUT" ||
            $this->_request->method === "POST") {
            $this->throwError(API_BAD_REQUEST,
                              _("To create a new codepoint, please mail it to unicode@unicode.org."));
        }

        if ($this->_action === '') {
            $this->_action = 'usage';
        }
        if (! file_exists(__DIR__."/api/{$this->_action}.php")) {
            $this->throwError(API_NOT_FOUND,
                              _("This API method does not exist."));
        }

        /* check some modification times and return a 304, if nothing
         * changed
         */
        $this->_mtime = max(filemtime(__DIR__."/api/{$this->_action}.php"),
                            filemtime(DB_PATH));
        if (array_key_exists("HTTP_IF_MODIFIED_SINCE", $_SERVER)) {
            if (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $this->_mtime) {
                $this->_sendHeaders(array('status' => '304'));
                exit();
            }
        }

        /* prepare environment */
        $api = $this;
        $this->_response = require __DIR__."/api/{$this->_action}.php";
    }

    /**
     * when errors exist, generate API-conform output
     */
    public function handleError() {
        if ($this->_error) {
            $host = get_origin().'api/v1';
            $status = 500;
            $content = array(
                "problemType" => "$host/problem/",
                "title" => _("An unknown error occured.")
            );
            switch($this->_error[0]) {
                case API_BAD_REQUEST:
                    $status = 400;
                    $content['title'] = $this->_error[1];
                    $content['problemType'] .= 'bad_request';
                    break;
                case API_NOT_FOUND:
                    $status = 404;
                    $content['title'] = $this->_error[1];
                    $content['problemType'] .= 'not_found';
                    break;
                case API_PRECONDITION_FAILED:
                    $status = 412;
                    $content['title'] = $this->_error[1];
                    $content['problemType'] .= 'precondition_failed';
                    break;
                case API_REQUEST_ENTITY_TOO_LARGE:
                    $status = 413;
                    $content['title'] = $this->_error[1];
                    $content['problemType'] .= 'request_entity_too_large';
                    break;
                case API_REQUEST_URI_TOO_LONG:
                    $status = 414;
                    $content['title'] = $this->_error[1];
                    $content['problemType'] .= 'request_uri_too_long';
                    break;
                default:
                    if ($this->_error[0] >= 400 && $this->_error[0] < 600) {
                        // assume it's an HTTP error code
                        $status = $this->_error[0];
                    }
                    if (count($this->_error) > 1) {
                        $content['title'] = $this->_error[1];
                    }
                    break;
            }

            if ($this->_mime === 'image/png' ||
                $this->_mime === 'text/plain') {
                $content = $content['title'];
            } elseif ($this->_mime === 'application/json') {
                if (array_key_exists('HTTP_ACCEPT', $_SERVER) &&
                    strpos($_SERVER['HTTP_ACCEPT'], 'application/api-problem+json') !== false) {
                    $this->_mime = 'application/api-problem+json';
                }
                if (count($this->_error) > 2 && $this->_error[2]) {
                    $content += $this->_error[2];
                }
            }
            $this->_finish($content, array("status" => $status));
        }
    }

    /**
     * intelligently encode $thing depending on the API's response MIME type
     */
    protected function _encode($thing) {
        switch ($this->_mime) {
            case 'text/plain':
                if (is_array($thing)) {
                    return join(",", $thing);
                } elseif (is_object($thing)) {
                    return $thing->__toString();
                } else {
                    return $thing;
                }
            case 'application/json':
            case 'application/api-problem+json':
            case 'application/javascript':
                return json_encode($thing, true);
            default:
                // hope, that $thing is already in the right format
                return $thing;
        }
    }

    /**
     * send appropriate HTTP headers
     *
     * Also sends CORS header
     */
    protected function _sendHeaders(Array $additional = array()) {
        header('Content-Type: '.$this->_mime.'; charset=UTF-8');
        header('Access-Control-Allow-Origin: *');
        header('Unicode-Version: '.UNICODE_VERSION);
        header('Content-Language: ' . L10n::getDefaultLanguage());

        header_remove('X-Powered-By');

        if ($this->_mtime) {
            header('Last-Modified: '.date("r", $this->_mtime));
        }

        foreach ($additional as $key => $value) {
            if ($key === 'status') {
                header("Status: $value", true, $value);
            } else {
                header("$key: $value");
            }
        }
    }

    /**
     * handle JSONP when response would be JSON and there's an appropriate
     * "callback" GET parameter
     */
    protected function _detectJSONP(&$data) {
        if (isset($_GET['callback']) &&
            preg_match('/^[_\$a-zA-Z][_\$\.a-zA-Z0-9]*$/', $_GET['callback']) &&
            in_array($this->_mime, array('application/json',
                'application/api-problem+json', 'application/javascript'))) {
            $this->_mime = 'application/javascript';
            $data = $_GET['callback'].'('.$data.');';
        }
    }

    /**
     * do the output and header sending
     */
    protected function _finish($content, $headers = array()) {
        $data = $this->_encode($content);

        $this->_detectJSONP($data);

        $this->_sendHeaders($headers);

        echo $data;
    }

}


#EOF
