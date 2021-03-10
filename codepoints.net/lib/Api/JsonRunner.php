<?php

namespace Codepoints\Api;

use Codepoints\Api\Runner;


abstract class JsonRunner extends Runner {

    public function handle(string $data) : string {
        $content = $this->handle_request($data);
        header('Content-Type: application/json; charset=utf-8');
        return json_encode($content);
    }

    /**
     * @return mixed
     */
    abstract protected function handle_request(string $data);

}
