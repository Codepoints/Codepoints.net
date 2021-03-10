<?php

namespace Codepoints\Api\Runner;

use Codepoints\Api\JsonRunner;
use Codepoints\Api\Exception as ApiException;


class Script extends JsonRunner {

    protected function handle_request(string $data) : Array {
        $all_scripts = $this->env['db']->getAll('SELECT iso, name FROM scripts');
        if (! $data) {
            $scripts = [];
            foreach ($all_scripts as $script) {
                $scripts[$script['iso']] = $script['name'];
            }
            return [
                'description' => __('Specify one or more ISO short names separated by comma. The response is a list of detail informations about these scripts.'),
                'iso' => '[A-Z][a-z]{3}',
                'scripts' => $scripts,
            ];
        }

        if (strlen($data) > 128 || ! preg_match('/^[A-Z][a-z]{3}(,[A-Z][a-z]{3})*$/', $data)) {
            throw new ApiException(__('No valid script names'), ApiException::BAD_REQUEST);
        }

        $db_data = $this->env['db']->getAll('
            SELECT name, abstract, src FROM scripts
            LEFT JOIN script_abstract ON (sc = iso)
            WHERE lang = ? AND sc IN ("'.str_replace(',', '","', $data).'")', $this->env['lang']);
        if (! $db_data) {
            throw new ApiException(__('No script data found'), ApiException::NOT_FOUND);
        }

        $response = array_map(function(Array $item) : Array {
            $item['abstract'] = strip_tags($item['abstract'], '<p><b><strong class="selflink"><strong><em><i><var><sup><sub><tt><ul><ol><li><samp><small><hr><h2><h3><h4><h5><dfn><dl><dd><dt><u><abbr><big><blockquote><br><center><del><ins><kbd>');
            return $item;
        }, $db_data);
        return $response;
    }

}
