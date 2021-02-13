<?php

namespace Codepoints;

use \Analog\Analog;

/**
 * a PDO extension that supports basic logging
 */
class Database extends \PDO {

    //public function prepare(string $query, Array $params=[]) : \PDOStatement {
    public function prepare($query, $params=[]) {
        $this->__log($query);
        return parent::prepare($query, $params);
    }

    public function query(string $query) : \PDOStatement {
        $this->__log($query);
        return parent::query(...func_get_args());
    }

    /**
     * helper method: get one result set as associative array
     */
    public function getOne(string $query_sql, ...$args) {
        $query = $this->prepare($query_sql);
        $query->execute($args);
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        $query->closeCursor();
        return $result;
    }

    /**
     * helper method: get all result sets as associative array
     */
    public function getAll(string $query_sql, ...$args) {
        $query = $this->prepare($query_sql);
        $query->execute($args);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        $query->closeCursor();
        return $result;
    }

    protected function __log(string $query) : void {
        Analog::log(preg_replace('/\s{2,}/', ' ', $query));
    }

}