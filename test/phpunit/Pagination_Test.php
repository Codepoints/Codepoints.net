<?php


require_once __DIR__.'/../../codepoints.net/lib/pagination.class.php';


class Pagination_Test extends PHPUnit_Framework_TestCase {

    function setUp() {
        $_GET = array();
    }

    function testShortPagination() {
        $pag = new Pagination(5);
        $this->assertEquals(1, $pag->getNumberOfPages(),
                            'only a single page');
        $this->assertEquals(array(0, 256), $pag->getLimits(),
                            'limits of [0, 256]');
        $this->assertEquals('', $pag->render(),
                            'empty rendering');
    }

    function testLongPagination() {
        $pag = new Pagination(500, 100);
        $this->assertEquals(5, $pag->getNumberOfPages(),
                            'five pages');
        $this->assertEquals(array(0, 100), $pag->getLimits(),
                            'limits of [0, 100]');
        $this->assertContains('class="pagination"', $pag->render(),
                            'non-empty rendering');
    }

    function testSetUrl() {
        $pag = new Pagination(50, 10);
        $pag->setUrl('?a=b&b=%s');
        $pag->setPage(3);
        $this->assertContains('?a=b&amp;b=2', $pag->render(),
                              'renders URL in output');
        $this->assertContains('?a=b&amp;b=1', $pag->render(),
                              'renders URL in output');
        $this->assertNotContains('?a=b&amp;b=0', $pag->render(),
                              'renders no out-of-bound URL in output');
        $this->assertNotContains('?a=b&amp;b=3', $pag->render(),
                              "doesn't render current URL in output");
    }

    function testGETPrefilling() {
        $_GET = array("a" => "b", "b[]" => "123");
        $pag = new Pagination(50, 10);
        $this->assertContains('?a=b&amp;b%5B%5D=123&amp;page=2', $pag->render(),
                              'renders URL in output');
        $this->assertNotContains('?a=b&amp;b%5B%5D=123&amp;page=1', $pag->render(),
                              "doesn't render current URL in output");
        $pag->setPage(2);
        $this->assertContains('?a=b&amp;b%5B%5D=123&amp;page=1', $pag->render(),
                              'renders URL in output');
        $this->assertNotContains('?a=b&amp;b%5B%5D=123&amp;page=2', $pag->render(),
                              "doesn't render current URL in output");
    }

    function testSlicing() {
        $pag = new Pagination(6, 2);
        $this->assertEquals(3, $pag->getNumberOfPages(),
                            'three pages');
        $test = array(1,2,3,4,5,6);
        $this->assertEquals(array(1, 2), $pag->getSet($test),
                            'slices first page');
        $pag->setPage(2);
        $this->assertEquals(array(3, 4), $pag->getSet($test),
                            'slices 2nd page');
        $pag->setPage(3);
        $this->assertEquals(array(5, 6), $pag->getSet($test),
                            'slices 3rd page');
        $pag->setPage(4);
        $this->assertEquals(array(), $pag->getSet($test),
                            'slices 4th page: empty array');
    }

}


#EOF
