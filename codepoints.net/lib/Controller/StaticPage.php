<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Controller\TraitPreload;
use Codepoints\Router\NotFoundException;
use Codepoints\View;


class StaticPage extends Controller {

    use TraitPreload;

    /**
     * @param string $view
     */
    public function __invoke($view, Array $env) : string {
        $this->sendPreloadHeaders();
        $context = [];
        switch ($view) {
        case 'about':
            $data = $env['db']->getOne('SELECT COUNT(*) AS c FROM codepoints USE INDEX (PRIMARY)');
            $context['cp_count'] = $data['c'];
            break;
        case 'glossary':
        case 'offline.html':
        case 'wp-login.php':
            break;
        default:
            throw new NotFoundException('This page is unknown');
        }
        return (new View(basename($view, '.html')))($context, $env);
    }

}
