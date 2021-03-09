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

    /**
     * @param string $view
     */
    public function __invoke($view, Array $env) : string {
        if (! array_key_exists($view, $this->views)) {
            throw new NotFoundException('This page is unknown');
        }
        return (new View($view))($this->views[$view], $env);
    }

}
