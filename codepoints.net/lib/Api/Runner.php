<?php

namespace Codepoints\Api;

abstract class Runner {

    protected Array $env;

    public function __construct(Array $env) {
        $this->env = $env;
    }

    abstract public function handle(string $data) : string;

}
