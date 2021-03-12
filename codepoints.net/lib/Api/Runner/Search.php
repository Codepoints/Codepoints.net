<?php

namespace Codepoints\Api\Runner;

use Codepoints\Api\JsonRunner;
use Codepoints\Api\Exception as ApiException;
use Codepoints\Controller\Search as SearchController;


class Search extends JsonRunner {

    protected function handle_request(string $data) : Array {
        if (! count($_GET)) {
            return [
                'description' => __('search for codepoints by their properties'),
                'search_url' => 'https://codepoints.net/api/v1/search{?property}{&page}{&per_page}{&callback}',
                'properties' => [
                    'q' => __('free search'),
                    'int' => __('decimal codepoint'),
                ] + array_keys($this->env['info']->properties),
            ];
        }

        $query = filter_input(INPUT_SERVER, 'QUERY_STRING');
        $controller = new SearchController();
        list($search_result, $pagination) = $controller->getSearchResult($query, $this->env);

        $limit = isset($_GET['per_page'])? min(1000, intval($_GET['per_page'])) : 1000;

        $page = $pagination? (int)$pagination->page : 1;
        $return = [
            'page' => $page,
            'last_page' => 1,
            'per_page' => $limit,
            'count' => $search_result? $search_result->count() : 0,
            'result' => [],
        ];

        if ($search_result && $return['count'] > 0) {
            $last_page = $pagination? $pagination->getNumberOfPages() : 1;
            $return['last_page'] = $last_page;
            $link_header = 'Link: <https://codepoints.net/api/v1/search?';
            header('Link: <https://codepoints.net/search?'.http_build_query($_GET).'>; rel=alternate', false);
            if ($page > 1 && $page <= $last_page) {
                $get = $_GET;
                $get['page'] = $page - 1;
                header($link_header.http_build_query($get).'>; rel=prev', false);
            } elseif ($page > $last_page) {
                $get = $_GET;
                $get['page'] = $last_page;
                header($link_header.http_build_query($get).'>; rel=prev', false);
            }
            if ($page < $last_page) {
                $get = $_GET;
                $get['page'] = $page + 1;
                header($link_header.http_build_query($get).'>; rel=next', false);
            }
            $get = $_GET;
            $get['page'] = $last_page;
            header($link_header.http_build_query($get).'>; rel=last', false);
            $get['page'] = 1;
            header($link_header.http_build_query($get).'>; rel=first', false);

            foreach ($search_result as $cp) {
                $return['result'][] = $cp;
            }
        }

        return $return;
    }

}
