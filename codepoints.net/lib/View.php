<?php

namespace Codepoints;


/**
 * render a view file with appropriate context
 */
class View {

    private string $view;

    private string $file;

    public function __construct(string $view) {
        $base = dirname(__DIR__);
        $this->view = $view;
        $this->file = sprintf('%s/views/%s.php', $base, $view);
        if (! is_file($this->file)) {
            throw new \Exception('View not found: ' . $this->file);
        }
    }

    /**
     * @psalm-suppress UnresolvableInclude
     */
    public function __invoke(Array $params=[], Array $env=[]) : string {
        $lang = 'en';
        extract($params, EXTR_PREFIX_INVALID, 'v');
        $view = $this->view;
        if ($env) {
            $lang = $env['lang'];
            $info = $env['info'];
        }
        ob_start();
        include($this->file);
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

}
