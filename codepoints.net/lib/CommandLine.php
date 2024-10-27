<?php

namespace Codepoints;

use Codepoints\Search\Documenter;
use Codepoints\Unicode\Codepoint;
use Codepoints\Unicode\CodepointInfo\Aliases;
use Codepoints\Unicode\CodepointInfo\CLDR;
use Codepoints\Unicode\CodepointInfo\CSUR;
use Codepoints\Unicode\CodepointInfo\Confusables;
use Codepoints\Unicode\CodepointInfo\Description;
use Codepoints\Unicode\CodepointInfo\Extra;
use Codepoints\Unicode\CodepointInfo\ImageSource;
use Codepoints\Unicode\CodepointInfo\OtherSites;
use Codepoints\Unicode\CodepointInfo\Pronunciation;
use Codepoints\Unicode\CodepointInfo\Properties;
use Codepoints\Unicode\CodepointInfo\Relatives;
use Codepoints\Unicode\CodepointInfo\Representation;
use Codepoints\Unicode\CodepointInfo\Sensitivity;
use Codepoints\Unicode\CodepointInfo\Wikipedia;


class CommandLine {

    public function __construct(public readonly Array $argv, private readonly Array $env) {
    }

    public function run() : int {
        $method = str_replace('-', '_', $this->argv[1] ?? 'help');
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method], ...array_slice($this->argv, 2));
        }
        echo "no matching method found\n";
        $this->help();
        return 1;
    }

    private function help() : int {
        echo <<<EOF
usage: index.php [build-search|help]

EOF;
        return 0;
    }

    private function build_search() : int {
        (new Documenter($this->env))->buildNext();
        return 0;
    }

    private function get_search_doc(string $cp) : int {
        echo (new Documenter($this->env))->create(get_codepoint(hexdec($cp), $this->env['db']));
        return 0;
    }

    private function describe(string $cp) : int {
        new Aliases($this->env);
        new CLDR($this->env);
        new CSUR($this->env);
        new Confusables($this->env);
        new Description($this->env);
        new Extra($this->env);
        new ImageSource($this->env);
        new OtherSites($this->env);
        new Pronunciation($this->env);
        new Properties($this->env);
        new Relatives($this->env);
        new Representation($this->env);
        new Sensitivity();
        new Wikipedia($this->env);
        $cp = preg_replace('/^u\+/i', '', $cp);
        $cpo = get_codepoint(hexdec($cp), $this->env['db']);
        echo preg_replace('/\s*\n\n\s+/', "\n\n", preg_replace('/[ \t]+/', ' ', strip_tags($cpo->description ?? "- no description -\n")));
        return 0;
    }

}
