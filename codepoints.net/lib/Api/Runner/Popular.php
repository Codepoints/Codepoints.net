<?php

namespace Codepoints\Api\Runner;

use DateTimeImmutable;
use DateInterval;
use Doctrine\Common\Cache\FilesystemCache;
use Codepoints\Api\JsonRunner;
use Codepoints\Api\Exception as ApiException;


class Popular extends JsonRunner {

    private int $count = 20;

    protected function handle_request(string $data) : Array {
        $cache = new FilesystemCache(dirname(dirname(dirname(__DIR__))).'/cache');
        if ($cache->contains('api_popular_list')) {
            $most_popular = $cache->fetch('api_popular_list');
        } else {
            $most_popular = $this->fetch_fresh();
            $cache->save('api_popular_list', $most_popular, 60*60);
        }
        return $most_popular;
    }

    private function fetch_fresh() : Array {
        $most_popular = [];
        $api_token = $this->env['config']['stats']['token'];
        $date2 = new DateTimeImmutable();
        $interval = new DateInterval('P2D');
        $date1 = $date2->sub($interval);
        $params = [
            'module' => 'API',
            'format' => 'json',
            'method' => 'Actions.getPageUrls',
            'idSite' => '4',
            'date' => sprintf('%s,%s', $date1->format('Y-m-d'), $date2->format('Y-m-d')),
            'period' => 'range',
            'filter_limit' => $this->count + 10, // we need only 20, but overfetch to cater for wrong matches
            'showColumns' => 'label,url',
            'filter_column' => 'label',
            'filter_pattern' => '^/U',
            'token_auth' => $api_token,
        ];
        $api_url = 'https://stats.codepoints.net/?' . http_build_query($params);
        $data = json_decode(file_get_contents($api_url), true);
        if ($data) {
            foreach ($data as $item) {
                if (preg_match('#^/U ([a-fA-F0-9]{4,6})(?:\?.*)?$#', $item['label'], $match)) {
                    $most_popular[] = hexdec($match[1]);
                    if (count($most_popular) >= $this->count) {
                        break;
                    }
                }
            }
        }
        return $most_popular;
    }

}
