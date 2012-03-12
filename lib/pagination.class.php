<?php


/**
 * handle pagination
 */
class Pagination {

    protected $page = 1;
    protected $pagesize = 100;
    protected $setCount;
    protected $showPages = 5;
    protected $showPrevNext = true;
    protected $urlTemplate = '?page=%s';

    public function __construct($set, $pagesize=256) {
        $this->pagesize = $pagesize;
        $this->setCount = $set;
        $r = $_GET;
        $r['page'] = '___page___';
        $r = str_replace('___page___', '%s', str_replace('%', '%%',
             http_build_query($r)));
        $this->setUrl('?'.$r);
    }

    public function setPage($page) {
        $this->page = $page;
    }

    public function getSet($set) {
        return array_slice($set, ($this->page - 1) * $this->pagesize,
                           $this->pagesize, true);
    }

    public function setUrl($url) {
        $this->urlTemplate = $url;
    }

    public function getNumberOfPages() {
        return intval(ceil($this->setCount / $this->pagesize));
    }

    public function __toString() {
        $n = $this->getNumberOfPages();
        $html = '';
        if ($n > 1) {
            $html .= '<ol class="pagination">';
            if ($this->showPrevNext) {
                if ($this->page > 1) {
                    $html .= sprintf('<li value="0" class="prev"><a href="'.$this->urlTemplate.'">previous</a></li>', $this->page - 1);
                } else {
                    $html .= '<li value="0" class="prev disabled"><span>previous</span>';
                }
            }
            $ellipsis = false;
            for ($i = 1; $i <= $n; $i++) {
                if ($i === $this->page) {
                    $html .= sprintf('<li class="current" value="%s"><span>%s</span></li>', $i, $i);
                    $ellipsis = false;
                } elseif ($i === 1 || $i === $n || abs($this->page - $i) < $this->showPages) {
                    $html .= sprintf('<li value="%s"><a href="'.$this->urlTemplate.'">%s</a></li>', $i, $i, $i);
                    $ellipsis = false;
                } elseif (! $ellipsis) {
                    $html .= '<li value="0" class="ellipsis">…</li>';
                    $ellipsis = true;
                }
            }
            if ($this->showPrevNext) {
                if ($this->page < $n) {
                    $html .= sprintf('<li value="0" class="next"><a href="'.$this->urlTemplate.'">next</a></li>', $this->page + 1);
                } else {
                    $html .= '<li value="0" class="next disabled"><span>next</span>';
                }
            }
            $html .= '</ol>';
        }
        return $html;
    }

    public function render() {
        return $this->__toString();
    }

}


//__END__
