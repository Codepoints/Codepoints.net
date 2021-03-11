<?php

namespace Codepoints\Api\Runner;

use Normalizer;
use PDO;
use Codepoints\Api\JsonRunner;
use Codepoints\Api\Exception as ApiException;


class Transform extends JsonRunner {

    private const MAXLENGTH = 1024;

    private const SUBACTIONS = ['lower', 'upper', 'title', 'mirror', 'nfc', 'nfd', 'nfkc', 'nfkd'];

    /**
     * @return string|Array
     */
    protected function handle_request(string $data) {
        if (strpos($data, '/') === false) {
            $possibilities = [
                'description' => __('transform a string to another according to a mapping, e.g., making all characters upper-case.'),
                'actions' => self::SUBACTIONS,
            ];
            foreach(self::SUBACTIONS as $part) {
                $possibilities["transform_{$part}_url"] = "https://codepoints.net/api/v1/transform/$part/{data}";
            }
            return $possibilities;
        }

        list($action, $input) = explode('/', $data, 2);
        $input = rawurldecode($input);

        if (mb_strlen($input) > self::MAXLENGTH) {
            throw new ApiException(
                sprintf(__('Request too long: Only %d characters allowed.'), self::MAXLENGTH),
                ApiException::REQUEST_URI_TOO_LONG);
        }

        if (! in_array($action, self::SUBACTIONS)) {
            throw new ApiException(
                sprintf(__('Unknown transform action %s'), $action),
                ApiException::BAD_REQUEST);
        }

        $codepoints = array_map(function (string $c) : int {
            return mb_ord($c);
        }, mb_str_split($input));

        $mapped_cps = [];
        switch ($action) {
            case 'lower':
                $mapped_cps = $this->unicode_to_utf8($this->map_by_db($codepoints, 'lc'));
                break;
            case 'upper':
                $mapped_cps = $this->unicode_to_utf8($this->map_by_db($codepoints, 'uc'));
                break;
            case 'title':
                $mapped_cps = $this->unicode_to_utf8($this->map_by_db($codepoints, 'tc'));
                break;
            case 'mirror':
                $mapped_cps = $this->unicode_to_utf8($this->map_by_db($codepoints, 'bmg'));
                break;
            case 'nfc':
                $mapped_cps = normalizer_normalize($input, Normalizer::FORM_C);
                break;
            case 'nfd':
                $mapped_cps = normalizer_normalize($input, Normalizer::FORM_D);
                break;
            case 'nfkc':
                $mapped_cps = normalizer_normalize($input, Normalizer::FORM_KC);
                break;
            case 'nfkd':
                $mapped_cps = normalizer_normalize($input, Normalizer::FORM_KD);
                break;
        }

        return $mapped_cps;
    }

    /**
     * map an array of codepoints to another array according to info from the
     * codepoint_relation table
     *
     * @param list<int> $codepoints
     * @param "lc"|"uc"|"tc"|"bmg" $relation
     * @return list<int>
     */
    private function map_by_db(Array $codepoints, string $relation) : Array {
        $stm = $this->env['db']->prepare('SELECT DISTINCT cp, other, `order`
            FROM codepoint_relation
            WHERE relation = ?
            AND cp != other
            AND cp IN ('.join(',', array_fill(0, count($codepoints), '?')).')');
        $stm_input = $codepoints;
        array_unshift($stm_input, $relation);
        $stm->execute($stm_input);
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
        $mapping = [];
        foreach ($result as $set) {
            if (! array_key_exists($set['cp'], $mapping)) {
                $mapping[$set['cp']] = [];
            }
            $mapping[$set['cp']][(int)$set['order']] = $set['other'];
        }

        $mapped_cps = [];
        foreach ($codepoints as $cp) {
            if (array_key_exists($cp, $mapping)) {
                $mapped_cps = array_merge($mapped_cps, $mapping[$cp]);
            } else {
                $mapped_cps[] = $cp;
            }
        }

        return $mapped_cps;
    }

    private function unicode_to_utf8(Array $codepoints) : string {
        return join('', array_map(function (int $i) : string {
            return mb_chr($i);
        }, $codepoints));
    }

}
