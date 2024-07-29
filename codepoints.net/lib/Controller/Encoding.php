<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Router\Pagination;
use Codepoints\Unicode\Block;
use Codepoints\Unicode\Codepoint;
use Codepoints\Unicode\SearchResult;
use Codepoints\Unicode\EncodingInfo;


class Encoding extends Controller {

    /**
     * @param string $match
     */
    public function __invoke($match, Array $env) : string {
        $page = get_page();

        $count = $env['db']->getOne('SELECT COUNT(*) AS count
            FROM codepoint_alias
            WHERE `type` LIKE ?', 'enc:'.$match[1])['count'];
        $aliases = [];
        $result = new SearchResult([
            'count' => $count,
            'items' => array_map(function (Array $entry) use (&$aliases) : Array {
            $aliases[$entry['cp']] = $entry['alias'];
            return $entry;
        }, $env['db']->getAll('SELECT cp, name, gc, `alias`
            FROM codepoints
            JOIN codepoint_alias
                USING (cp)
            WHERE `type` LIKE ?
            ORDER BY `alias`
            LIMIT ?, ?',
            'enc:'.$match[1],
            ($page - 1) * Pagination::PAGE_SIZE, Pagination::PAGE_SIZE)),
        ], $env['db']);

        $blocks = [];
        foreach ($env['db']->getAll('SELECT name, first, last FROM blocks') as $block_data) {
            foreach ($result as $codepoint) {
                if ($codepoint && $codepoint->id >= $block_data['first'] && $codepoint->id <= $block_data['last']) {
                    if (! array_key_exists($block_data['first'], $blocks)) {
                        $blocks[$block_data['first']] = [
                            'count' => 0,
                            'block' => new Block($block_data, $env['db']),
                        ];
                    }
                    $blocks[$block_data['first']]['count'] += 1;
                }
            }
        }

        $label = EncodingInfo::getLabel($match[1]);
        $this->context += [
            'title' => sprintf(__('Encoding %s'), $label),
            'label' => $label,
            'page_description' => sprintf(__('Browse codepoints covered by the encoding %s.'), $match[1]),
            'pagination' => new Pagination($result, $page),
            'result' => $result,
            'aliases' => $aliases,
            'blocks' => $blocks,
        ];
        return parent::__invoke($match, $env);
    }

}
