<?php

namespace Codepoints\Api\Runner;

use DateTimeImmutable;
use DateInterval;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Codepoints\Api\JsonRunner;
use Codepoints\Api\Exception as ApiException;
use Codepoints\Unicode\Codepoint;


class Popular extends JsonRunner {

    private int $count = 20;

    protected function handle_request(string $data) : Array {
        $cache = new FilesystemAdapter('codepts');
        return $cache->get('api_popular_list', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            return $this->fetch_fresh();
        });
    }

    private function fetch_fresh() : Array {
        $intlist = [];
        $api_url = 'https://stats.codepoints.net/popular.php';
        $data = json_decode(file_get_contents($api_url), true);
        if ($data) {
            foreach ($data as $item) {
                if (preg_match('#^/U ([a-fA-F0-9]{4,6})(?:\?.*)?$#', $item['label'], $match)) {
                    $intlist[] = hexdec($match[1]);
                }
            }
        }
        $cps = [];
        $list = join(',', array_unique($intlist));
        $data = $this->env['db']->getAll('SELECT cp, name, gc FROM codepoints
            WHERE cp IN ( '.$list.' ) ORDER BY FIELD( cp, '.$list.' )');
        if ($data) {
            foreach ($data as $set) {
                $cp = Codepoint::getCached($set, $this->env['db']);
                $image_generator = $cp->image;
                $cps[] = [
                    $cp->id,
                    $image_generator(250),
                    $cp->name,
                    $cp->gc
                ];
            }
        }
        return $cps;
    }

}
