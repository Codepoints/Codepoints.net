<?php


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
        $this->_finish($this->_response);
    }

    /**
     * generate an API error to forcefully jump to $this->handleError
     */
    public function throwError($code, $message) {
        $this->_error = array($code, $message);
        throw new APIException($message);
    }

    /**
     * run the API action and collect response and errors
     */
    public function run($data = null) {
        if (! file_exists(__DIR__."/api/{$this->_action}.php")) {
            $this->throwError(API_PREREQUISITE_MISSING,
                              _("This API method does not exist."));
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
            $status = 500;
            $content = array(
                "error" => true,
                "message" => _("An unknown error occured.")
            );
            switch($this->_error[0]) {
                case API_PREREQUISITE_MISSING:
                    $status = 404;
                    $content['message'] = $this->_error[1];
                    break;
                case API_REQUEST_TOO_LONG:
                    $status = 414;
                    $content['message'] = $this->_error[1];
                    break;
                case API_NOT_FOUND:
                    $status = 404;
                    $content['message'] = $this->_error[1];
                    break;
                case API_BAD_REQUEST:
                    $status = 400;
                    $content['message'] = $this->_error[1];
                    break;
                default:
                    if ($this->_error[0] >= 400 && $this->_error[0] < 600) {
                        // assume it's an HTTP error code
                        $status = $this->_error[0];
                    }
                    if (count($this->_error) > 1) {
                        $content['message'] = $this->_error[1];
                    }
                    break;
            }

            if ($this->_mime === 'image/png') {
                $content = $content['message'];
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
                }
                if (is_object($thing)) {
                    return $thing->__toString();
                }
                return $thing;
            case 'application/json':
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
        foreach ($additional as $key => $value) {
            if ($key === 'status') {
                header('', true, $value);
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
            preg_match('/^[_\$a-zA-Z][_\$a-zA-Z0-9]*$/', $_GET['callback']) &&
            $this->_mime === 'application/json') {
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
