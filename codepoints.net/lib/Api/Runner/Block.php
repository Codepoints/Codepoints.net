<?php

namespace Codepoints\Api\Runner;

use Codepoints\Api\JsonRunner;
use Codepoints\Api\Exception as ApiException;
use Codepoints\Unicode\Block as UnicodeBlock;


class Block extends JsonRunner {

    protected function handle_request(string $data) : Array {
        if (! $data) {
            return [
                'description' => __('show detailed information about a Unicode block'),
                'block_url' => 'https://codepoints.net/api/v1/block/{block}',
                'block' => array_map(function(Array $item) : string {
                    return str_replace(' ', '_', strtolower($item['name']));
                }, $this->env['db']->getAll('SELECT name FROM blocks')),
            ];
        }

        if (strlen($data) > 72 || preg_match('/[^a-zA-Z0-9_-]/', $data)) {
            throw new ApiException(__('No valid block name'), ApiException::BAD_REQUEST);
        }

        $db_data = $this->env['db']->getOne("
            SELECT name, first, last FROM blocks
            WHERE replace(replace(lower(name), '_', ''), ' ', '') = ?
            LIMIT 1", str_replace([' ', '_'], '', strtolower($data)));
        if (! $db_data) {
            throw new ApiException(__('Not a block name'), ApiException::NOT_FOUND);
        }
        $block = new UnicodeBlock($db_data, $this->env['db']);

        $return = [
            'name' => $block->name,
            'first' => $block->first,
            'last' => $block->last,
        ];

        header(sprintf('Link: <https://codepoints.net%s>; rel=alternate', url($block)), false);
        $plane = $block->plane;
        if ($plane) {
            header(sprintf('Link: <https://codepoints.net/api/v1/plane%s>; rel=up', url($plane)), false);
        }
        $next = $block->next;
        if ($next) {
            header(sprintf('Link: <https://codepoints.net/api/v1/block%s>; rel=next', url($next)), false);
            $return['next_block'] = $next->name;
        }
        $prev = $block->prev;
        if ($prev) {
            header(sprintf('Link: <https://codepoints.net/api/v1/block%s>; rel=prev', url($prev)), false);
            $return['prev_block'] = $prev->name;
        }

        return $return;
    }

}
