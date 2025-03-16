<?php

namespace Codepoints\Api\Runner;

use PDO;
use Codepoints\Api\JsonRunner;
use Codepoints\Api\Exception as ApiException;


class Filter extends JsonRunner {

    private const int MAXLENGTH = 1024;

    /**
     * @return string|Array
     */
    protected function handle_request(string $data) {
        $properties = array_keys($this->env['info']->properties);

        if (! $data) {
            return [
                'description' => __('Filter a string of characters by Unicode property. You can negate properties by appending a “!” to it: filter/string?age!=5.5 finds all characters in “string” that were *not* added in Unicode 5.5.'),
                'filter_url' => 'https://codepoints.net/api/v1/filter/{data}{?property*}',
                'property' => $properties,
            ];
        }
        $data = rawurldecode($data);

        if (mb_strlen($data) > self::MAXLENGTH) {
            throw new ApiException(
                sprintf(__('Request too long: Only %d characters allowed.'), self::MAXLENGTH),
                ApiException::REQUEST_URI_TOO_LONG);
        }

        $codepoints = array_map(function (string $c) : int {
            return mb_ord($c);
        }, mb_str_split($data));

        $sql_filter = [];
        $values = [];

        foreach ($_GET as $property => $value) {
            if ($property === 'callback') {
                continue;
            }
            if (is_int($property)) {
                $property = (string)$property;
            }
            $not = '';
            if (substr($property, -1) === '!') {
                $not = ' NOT ';
                $property = substr($property, 0, -1);
            }
            if (! in_array($property, $properties) || preg_match('/[^a-zA-Z0-9_]/', $property)) {
                throw new ApiException(sprintf(__('Cannot filter for unknown property %s'), $property),
                    ApiException::BAD_REQUEST);
            }
            $value = (array)$value;
            $sql_filter[] = sprintf('%s %s IN (%s)', $property, $not,
                    join(',', array_fill(0, count($value), '?')));
            $values = array_merge($values, $value);
        }

        $stm = $this->env['db']->prepare('SELECT cp FROM codepoint_props WHERE
            cp IN ('.join(',', $codepoints).') AND '
            .join(' AND ', $sql_filter));

        if (! $stm) {
            throw new ApiException(__('Cannot filter.'), ApiException::INTERNAL_SERVER_ERROR);
        }

        $stm->execute($values);
        $filtered_cps = $stm->fetchAll(PDO::FETCH_COLUMN, 0);

        $codepoints = array_filter($codepoints, function(int $cp) use ($filtered_cps) {
            return in_array($cp, $filtered_cps);
        });

        return join('', array_map(function (int $i) : string {
            return mb_chr($i);
        }, $codepoints));
    }

}
