<?php

namespace Codepoints\Api\Runner;

use Codepoints\Api\JsonRunner;
use Codepoints\Api\Exception as ApiException;
use Codepoints\Unicode\CodepointInfo\Aliases;
use Codepoints\Unicode\CodepointInfo\CLDR;
use Codepoints\Unicode\CodepointInfo\Confusables;
use Codepoints\Unicode\CodepointInfo\Description;
use Codepoints\Unicode\CodepointInfo\Extra;
use Codepoints\Unicode\CodepointInfo\ImageSource;
use Codepoints\Unicode\CodepointInfo\OtherSites;
use Codepoints\Unicode\CodepointInfo\Pronunciation;
use Codepoints\Unicode\CodepointInfo\Properties;
use Codepoints\Unicode\CodepointInfo\Relatives;
use Codepoints\Unicode\CodepointInfo\Representation;
use Codepoints\Unicode\CodepointInfo\Wikipedia;


class Codepoint extends JsonRunner {

    protected function handle_request(string $data) : Array {
        if (! $data) {
            return [
                'description' => __('show detailed information about a single codepoint. You can specify fields of interest with the “property” parameter: codepoint/1234?property=age,uc,lc'),
                'codepoint_url' => 'https://codepoints.net/api/v1/codepoint/{codepoint}{?property*}',
                'codepoint' => '(10)?[A-Fa-f0-9]{4}|0[A-Fa-f0-9]{5}',
                'property' => array_keys($this->env['info']->properties),
            ];
        }

        if (strlen($data) > 6 || ! ctype_xdigit($data)) {
            throw new ApiException(__('No valid codepoint'), ApiException::BAD_REQUEST);
        }

        $codepoint = get_codepoint(hexdec($data), $this->env['db']);
        if (! $codepoint) {
            throw new ApiException(__('Not a codepoint'), ApiException::NOT_FOUND);
        }

        header(sprintf('Link: <https://codepoints.net/U+%04X>; rel=alternate', $codepoint->id), false);
        $block = $codepoint->block;
        if ($block) {
            header(sprintf('Link: <https://codepoints.net/api/v1/block%s>; rel=up', url($block)), false);
        }
        $next = $codepoint->next;
        if ($next) {
            header(sprintf('Link: <https://codepoints.net/api/v1/codepoint/%04X>; rel=next', $next->id), false);
        }
        $prev = $codepoint->prev;
        if ($prev) {
            header(sprintf('Link: <https://codepoints.net/api/v1/codepoint/%04X>; rel=prev', $prev->id), false);
        }

        new Aliases($this->env);
        new CLDR($this->env);
        new Confusables($this->env);
        new Description($this->env);
        new Extra($this->env);
        new ImageSource($this->env);
        new OtherSites($this->env);
        new Pronunciation($this->env);
        new Properties($this->env);
        new Relatives($this->env);
        new Representation($this->env);
        new Wikipedia($this->env);
        $response = [ 'cp' => $codepoint->id ] + $codepoint->properties + [ '_' => [] ];

        if (isset($_GET['property']) && is_string($_GET['property'])) {
            $mask = array_filter(explode(',', $_GET['property']));
            if (count($mask)) {
                $response = array_intersect_key($response, array_flip($mask));
            }
        }
        if (array_key_exists('_', $response)) {
            $response['_']['description'] = $codepoint->description;
            $response['_']['image'] = ($codepoint->image)();
            $response['_']['imagesource'] = $codepoint->imagesource['font'] ?? null;
            $response['_']['wikipedia'] = $codepoint->wikipedia;
        }
        return $response;
    }

}
