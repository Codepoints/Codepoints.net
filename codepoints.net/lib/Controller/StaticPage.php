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
        ];
    }

    public function __invoke(string $view, array $env) : string {
        if (! array_key_exists($view, $this->views)) {
            throw new NotFoundException('This page is unknown');
        }
        if ($view === 'about') {
            $data = $env['db']->getOne('
                SELECT age
                    FROM codepoint_props
                ORDER BY CAST(age AS FLOAT) DESC
                LIMIT 1');
            $this->views[$view]['unicode_version'] = $data? $data['age'] : '?';
        }
        return (new View($view))($this->views[$view], $env);
    }

}
