<?php
namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Router\Redirect;

class PossibleName extends Controller {

    /**
     * @param Array $data
     */
    public function __invoke($data, Array $env) : string {
        throw new Redirect(sprintf('U+%04X', $data['cp']));
        return '';
    }
}
