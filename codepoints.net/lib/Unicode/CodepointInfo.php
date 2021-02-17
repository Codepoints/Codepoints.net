<?php

namespace Codepoints\Unicode;

use \Codepoints\Unicode\Codepoint;
use \Codepoints\Database;


/**
 * base class for info providers for code points
 */
abstract class CodepointInfo {

    protected Database $db;
    protected string $lang;

    public function __construct(Database $db, string $lang) {
        $name = strtolower(preg_replace('/.*\\\\/', '', get_class($this)));
        Codepoint::addInfoProvider($name, $this);
        $this->db = $db;
        $this->lang = $lang;
    }

    /**
     * @return mixed
     */
    abstract public function __invoke(Codepoint $codepoint);

}
