<?php

/**
 * a PDO extension that supports basic logging
 */
class DB extends PDO {
    public function prepare($query, $params=array()) {
        $this->__log($query);
        return parent::prepare($query, $params);
    }
    public function query($query) {
        $this->__log($query);
        return call_user_func_array(array(get_parent_class($this), 'query'),
                                    func_get_args());
    }
    protected function __log($query) {
        flog(preg_replace('/\s{2,}/', ' ', $query));
    }
}


//__END__
