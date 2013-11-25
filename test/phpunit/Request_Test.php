<?php


require_once __DIR__.'/../../codepoints.net/lib/request.class.php';


class Request_Test extends PHPUnit_Framework_TestCase {

    function setUp() {
        $_GET = array();
        $_SERVER = array();
    }

    function testBasics() {
        $req = new Request('/foo/bar.html');
        $this->assertEquals('GET', $req->method, 'HTTP method');
        $this->assertEquals('text/html', $req->type, 'MIME type');
        $this->assertEquals('en', $req->lang, 'language');
        $this->assertEquals('/foo/bar.html', $req->url, 'deduced URL');
        $this->assertEquals('/foo/bar', $req->trunkUrl, 'deduced trunk URL');
        $_SERVER = array("REQUEST_URI" => '/foo/bar.html?baz');
        $req = new Request();
        $this->assertEquals('GET', $req->method, 'HTTP method');
        $this->assertEquals('text/html', $req->type, 'MIME type');
        $this->assertEquals('en', $req->lang, 'language');
        $this->assertEquals('/foo/bar.html?baz', $req->url, 'deduced URL');
        $this->assertEquals('/foo/bar', $req->trunkUrl, 'deduced trunk URL');
    }

    function testHTTPMethod() {
        $_SERVER = array("REQUEST_METHOD" => "get");
        $req = new Request('/');
        $this->assertEquals('GET', $req->method, 'lowercase HTTP method');
        $_SERVER = array("REQUEST_METHOD" => "POST");
        $req = new Request('/');
        $this->assertEquals('POST', $req->method, 'POST method');
        $_SERVER = array("REQUEST_METHOD" => "QUARK");
        $req = new Request('/');
        $this->assertEquals('GET', $req->method, 'non-existing HTTP method');
    }

    function testType() {
        $_SERVER = array("HTTP_ACCEPT" => "application/json");
        $req = new Request('/foo.json');
        $this->assertEquals('application/json', $req->type, 'type JSON');
        $_SERVER = array("HTTP_ACCEPT" => "application/json");
        $req = new Request('/foo');
        $this->assertEquals('application/json', $req->type, 'type JSON');
        $_SERVER = array("HTTP_ACCEPT" => "application/json");
        $req = new Request('/foo.html');
        $this->assertEquals('text/html', $req->type, 'forced type HTML');
        $_SERVER = array("HTTP_ACCEPT" => "text/html,application/json");
        $req = new Request('/foo.html');
        $this->assertEquals('text/html', $req->type, 'negotiated type HTML');
    }

}


#EOF
