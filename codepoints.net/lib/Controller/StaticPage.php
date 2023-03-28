<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Router\NotFoundException;
use Codepoints\View;


class StaticPage extends Controller {
    /**
     * @param string $view
     */
    public function __invoke($view, Array $env) : string {
        switch ($view) {
        case 'about':
            $data = $env['db']->getOne('SELECT COUNT(*) AS c FROM codepoints USE INDEX (PRIMARY)');
            $context = [
                'title' => __('About Codepoints'),
                'page_description' => __('Codepoints is a site dedicated to Unicode. This page explains the concepts and possibilities to navigate Unicode on the site.'),
                'cp_count' => $data['c'],
            ];
            break;
        case 'glossary':
            $context = [
                'title' => __('Glossary of Terms'),
                'page_description' => __('This glossary explains central terms of the Unicode standard and character encodings in general.'),
            ];
            break;
        case 'offline.html':
            $context = [
                'title' => __('You are off-line'),
                'page_description' => __('You are currently off-line. Pages, that you visited recently, should still be available, though.'),
            ];
            break;
        default:
            throw new NotFoundException('This page is unknown');
        }
        return (new View(basename($view, '.html')))($context, $env);
    }

}
