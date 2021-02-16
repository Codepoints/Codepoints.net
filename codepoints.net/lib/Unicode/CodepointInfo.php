<?php

namespace Codepoints\Unicode;

use \Codepoints\Unicode\Codepoint;
use \Codepoints\Database;


/**
 * base class for info providers for code points
 */
abstract class CodepointInfo {

    protected Database $db;

    public function __construct(Database $db) {
        $name = strtolower(preg_replace('/.*\\\\/', '', get_class($this)));
        Codepoint::addInfoProvider($name, $this);
        $this->db = $db;
    }

    /**
     * @return mixed
     */
    abstract public function __invoke(Codepoint $codepoint, Array $args);

}
