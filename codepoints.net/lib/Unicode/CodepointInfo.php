<?php

namespace Codepoints\Unicode;

use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\PropertyInfo;
use \Codepoints\Database;


/**
 * base class for info providers for code points
 */
abstract class CodepointInfo {

    protected Database $db;
    protected string $lang;
    protected PropertyInfo $info;

    /**
     * @param Array{db: Database, lang: string, info: PropertyInfo} $env
     */
    public function __construct(Array $env) {
        $this->db = $env['db'];
        $this->lang = $env['lang'];
        $this->info = $env['info'];
        $name = strtolower(preg_replace('/.*\\\\/', '', get_class($this)));
        Codepoint::addInfoProvider($name, $this);
    }

    /**
     * @return mixed
     */
    abstract public function __invoke(Codepoint $codepoint);

}
