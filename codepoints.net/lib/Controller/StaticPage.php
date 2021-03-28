<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Router\NotFoundException;
use Codepoints\View;


class StaticPage extends Controller {

    private Array $views;

    public function __construct() {
        $this->views = [
            'about' => [
                'title' => __('About Codepoints'),
                'page_description' => __('Codepoints is a site dedicated to Unicode. This page explains the concepts and possibilities to navigate Unicode on the site.'),
            ],
            'glossary' => [
                'title' => __('Glossary of Terms'),
                'page_description' => __('This glossary explains central terms of the Unicode standard and character encodings in general.'),
            ],
            'offline' => [
                'title' => __('You are off-line'),
                'page_description' => __('You are currently off-line. Pages, that you visited recently, should still be available, though.'),
            ],
        ];
    }

    /**
     * @param string $view
     */
    public function __invoke($view, Array $env) : string {
        if (! array_key_exists($view, $this->views)) {
            throw new NotFoundException('This page is unknown');
        }
        if ($view === 'about') {
            $data = $env['db']->getOne('SELECT COUNT(*) AS c FROM codepoints USE INDEX (PRIMARY)');
            $this->views['about']['cp_count'] = $data['c'];
        }
        return (new View($view))($this->views[$view], $env);
    }

}
