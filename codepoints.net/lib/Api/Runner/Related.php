<?php

namespace Codepoints\Api\Runner;

use Codepoints\Api\JsonRunner;
use Codepoints\Api\Exception as ApiException;


class Related extends JsonRunner {

    protected function handle_request(string $data) : Array {
        if (! $data) {
            return [
                'description' => __('get related codepoints'),
                'related_url' => 'https://codepoints.net/api/v1/codepoint/{codepoint}{?type*}',
                'codepoint' => '(10)?[A-Fa-f0-9]{4}|0[A-Fa-f0-9]{5}',
                'types' => [],
            ];
        }

        if (strlen($data) > 6 || ! ctype_xdigit($data)) {
            throw new ApiException(__('No valid codepoint'), ApiException::BAD_REQUEST);
        }

        $codepoint = get_codepoint(hexdec($data), $this->env['db']);
        if (! $codepoint) {
            throw new ApiException(__('Not a codepoint'), ApiException::NOT_FOUND);
        }

        $db_data = $this->env['db']->getAll('
            SELECT cp, relation, `order`
            FROM codepoint_relation
            WHERE other = ? AND cp != ?
        ', $codepoint->id, $codepoint->id);

        $relatives = [];
        foreach ($db_data as $res) {
            if (! array_key_exists($res['relation'], $relatives)) {
                $relatives[$res['relation']] = [];
            }
            $relatives[$res['relation']][(int)$res['order']] = (int)$res['cp'];
        }

        $db_data = $this->env['db']->getAll('
            SELECT cp, `order`
            FROM codepoint_confusables
            WHERE other = ?', $codepoint->id);
        foreach ($db_data as $res) {
            $relatives['confusables'][] = [
                'cp' => $res['cp'],
                'order' => $res['order'],
            ];
        }

        return $relatives;
    }

}
