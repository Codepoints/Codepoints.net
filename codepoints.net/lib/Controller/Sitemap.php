<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Unicode\Block;
use Codepoints\Unicode\Codepoint;


class Sitemap extends Controller {

    /**
     * @param Array $match
     */
    public function __invoke($match, Array $env) : string {
        header('Content-Type: application/xml; charset=utf-8');

        $is_index = false;
        $this->context['data'] = [];

        if (! $match[1]) {
            $is_index = true;
            /* get counted batches of 0x100 code points as base for our
             * sitemap */
            $data = $env['db']->getAll('
                SELECT HEX(ANY_VALUE(cp - cp % 0x100)) first, COUNT(*) count
                    FROM codepoints
                GROUP BY (cp - cp % 0x100)');
            $this->context['data'][] = 'sitemap/static.xml';
            foreach ($data as $row) {
                $this->context['data'][] = sprintf('sitemap/u%04s.xml', $row['first']);
            }

        } elseif ($match[1] === '/static') {
            $this->context['data'] = [
                [
                    'loc' => '',
                    'priority' => '0.9',
                ],
                [
                    'loc' => 'random',
                    'changefreq' => 'always',
                    'priority' => '0',
                ],
                [
                    'loc' => 'about',
                    'priority' => '0.6',
                ],
                [
                    'loc' => 'glossary',
                    'priority' => '0.6',
                ],
                [
                    'loc' => 'scripts',
                    'priority' => '0.6',
                ],
                [
                    'loc' => 'search',
                    'priority' => '0.1',
                ],
                [
                    'loc' => 'planes',
                    'priority' => '0.4',
                ],
                [ 'loc' => 'basic_multilingual_plane' ],
                [ 'loc' => 'supplementary_multilingual_plane' ],
                [ 'loc' => 'supplementary_ideographic_plane' ],
                [ 'loc' => 'tertiary_ideographic_plane' ],
                [ 'loc' => 'plane_5_(unassigned)' ],
                [ 'loc' => 'plane_6_(unassigned)' ],
                [ 'loc' => 'plane_7_(unassigned)' ],
                [ 'loc' => 'plane_8_(unassigned)' ],
                [ 'loc' => 'plane_9_(unassigned)' ],
                [ 'loc' => 'plane_10_(unassigned)' ],
                [ 'loc' => 'plane_11_(unassigned)' ],
                [ 'loc' => 'plane_12_(unassigned)' ],
                [ 'loc' => 'plane_13_(unassigned)' ],
                [ 'loc' => 'plane_14_(unassigned)' ],
                [ 'loc' => 'supplementary_special-purpose_plane' ],
                [ 'loc' => 'supplementary_private_use_area_-_a_plane' ],
                [ 'loc' => 'supplementary_private_use_area_-_b_plane' ],
                [ 'loc' => 'humans.txt', ],
                [ 'loc' => 'opensearch.xml', 'priority' => '0.1', ],
            ];

        } elseif (substr($match[1], 0, 2) === '/u') {
            $int = hexdec(substr($match[1], 2));
            $this->context['data'] = [];
            $data = $env['db']->getAll('SELECT cp FROM codepoints WHERE cp >= ? AND cp < ?',
                $int, $int + 0x100
            );
            foreach ($data as $cp) {
                $this->context['data'][] = [ 'loc' => sprintf('U+%04X', $cp['cp']) ];
            }
            $data = $env['db']->getAll('SELECT name FROM blocks WHERE first >= ? AND first < ?',
                $int, $int + 0x100
            );
            foreach ($data as $blk) {
                $this->context['data'][] = [ 'loc' => str_replace(' ', '_', strtolower($blk['name'])) ];
            }
        }

        $this->context['is_index'] = $is_index;
        return parent::__invoke($match, $env);
    }

}
