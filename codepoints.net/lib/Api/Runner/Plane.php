<?php

namespace Codepoints\Api\Runner;

use Codepoints\Api\JsonRunner;
use Codepoints\Api\Exception as ApiException;
use Codepoints\Unicode\Block;
use Codepoints\Unicode\Plane as UnicodePlane;


class Plane extends JsonRunner {

    protected function handle_request(string $data) : Array {
        if (! $data) {
            return [
                'description' => __('show information about a Unicode plane'),
                'plane_url' => 'https://codepoints.net/api/v1/plane/{plane}',
                'plane' => array_map(function(Array $item) : string {
                    $base = str_replace(' ', '_', strtolower($item['name']));
                    return $base;
                }, $this->env['db']->getAll('SELECT name FROM planes')),
            ];
        }

        if (strlen($data) > 32 || preg_match('/[^a-zA-Z0-9_()-]/', $data)) {
            throw new ApiException(__('No valid plane name'), ApiException::BAD_REQUEST);
        }

        $db_data = $this->env['db']->getOne("
            SELECT name, first, last FROM planes
            WHERE replace(replace(lower(name), '_', ''), ' ', '') = ?
            LIMIT 1", str_replace([' ', '_'], '', strtolower($data)));
        if (! $db_data) {
            throw new ApiException(__('Not a plane name'), ApiException::NOT_FOUND);
        }
        $plane = new UnicodePlane($db_data, $this->env['db']);

        $return = [
            'name' => $plane->name,
            'first' => $plane->first,
            'last' => $plane->last,
            'blocks' => array_map(function(Block $block) : string {
                return $block->name;
            }, $plane->blocks),
        ];

        header(sprintf('Link: <https://codepoints.net%s>; rel=alternate', url($plane)), false);
        $next = $plane->next;
        if ($next) {
            header(sprintf('Link: <https://codepoints.net/api/v1/plane%s>; rel=next', url($next)), false);
            $return['next_plane'] = $next->name;
        }
        $prev = $plane->prev;
        if ($prev) {
            header(sprintf('Link: <https://codepoints.net/api/v1/plane%s>; rel=prev', url($prev)), false);
            $return['prev_plane'] = $prev->name;
        }

        return $return;
    }

}
