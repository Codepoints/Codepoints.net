<?php

namespace Codepoints;

use Codepoints\Search\Documenter;


class CommandLine {

    public function __construct(public readonly Array $argv, private readonly Array $env) {
    }

    public function run() {
        $method = str_replace('-', '_', $this->argv[1] ?? 'help');
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }
        echo "no matching method found\n";
        $this->help();
        return 1;
    }

    private function help() {
        echo <<<EOF
usage: index.php [build-search|help]

EOF;
    }

    private function build_search() {
        (new Documenter($this->env))->buildNext();
    }

}
