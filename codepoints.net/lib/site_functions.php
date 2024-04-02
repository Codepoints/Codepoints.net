<?php

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Codepoints\Unicode\Codepoint;
use Codepoints\Unicode\CodepointInfo\Sensitivity;
use Codepoints\Unicode\CodepointInfo\SENSITIVITY_LEVEL;
use Codepoints\Database;


/**
 * fetch and cache a list of the most popular code points from our stats site
 *
 * @return list<Codepoint>
 */
function get_popular_codepoints(Database $db) : Array {
    $fetch_fresh = function() use ($db) : Array {
        $intlist = [];
        $api_url = 'https://stats.codepoints.net/popular.php';
        if (is_file('/var/cache/codepoints/popular.json')) {
            /* this file is updated via cron job. If it is there, use this
             * instead of doing our own HTTP request. */
            $api_url = '/var/cache/codepoints/popular.json';
        }
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
        $data = $db->getAll('SELECT cp, name, gc FROM codepoints
            WHERE cp IN ( '.$list.' ) ORDER BY FIELD( cp, '.$list.' )');
        if (is_array($data)) {
            foreach ($data as $set) {
                $candidate = Codepoint::getCached($set, $db);
                if ($candidate->sensitivity->value > SENSITIVITY_LEVEL::RAISED->value) {
                    continue;
                }
                $cps[] = $candidate;
                if (count($cps) >= 100) {
                    // hard cut after the top 100
                    break;
                }
            }
        }
        return $cps;
    };

    $cache = new FilesystemAdapter('codepts');
    return $cache->get('api_popular_list', function (ItemInterface $item) use ($fetch_fresh) {
        // cache the list for 6 hours, so we refresh it 4x a day
        $item->expiresAfter(6 * 3600);
        return $fetch_fresh();
    });
}
