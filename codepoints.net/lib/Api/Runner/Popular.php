<?php

namespace Codepoints\Api\Runner;

use Codepoints\Api\JsonRunner;


class Popular extends JsonRunner {

    private int $count = 20;

    protected function handle_request(string $data) : Array {
        return array_map(
            function ($cp) {
                $image_generator = $cp->image;
                return [
                    $cp->id,
                    $image_generator(250),
                    $cp->name,
                    $cp->gc
                ];
            },
            array_slice(\get_popular_codepoints($this->env['db']), 0, $this->count)
        );
    }

}
