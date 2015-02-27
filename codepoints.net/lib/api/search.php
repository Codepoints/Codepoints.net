<?php

require_once __DIR__.'/../tools.php';

if (! count($_GET)) {
    $host = get_origin().'api/v1';
    return array(
        "description" => _("search for codepoints by their properties"),
        "search_url" => "$host/search{?property}{&page}{&per_page}{&callback}",
        "properties" => array(
            "q" => _("free search"),
            "int" => _("decimal codepoint"),
        ) + UnicodeInfo::get()->getAllCategories(),
    );
}

$q = new SearchComposer($_GET, $api->_db);
$result = $q->getSearchResult();

$page = isset($_GET['page'])? intval($_GET['page']) : 1;
$limit = isset($_GET['per_page'])? min(1000, intval($_GET['per_page'])) : 1000;
$result->pageLength = $limit;
$result->page = $page - 1;

$return = array(
    "page" => $page,
    "last_page" => 1,
    "per_page" => $limit,
    "count" => $result->getCount(),
    "result" => array(),
);

if ($return['count'] > 0) {
    $pagination = new Pagination($result->getCount(), $limit);
    $pagination->setPage($page);
    $last_page = $pagination->getNumberOfPages();
    $return["last_page"] = $last_page;
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

    foreach ($result->get() as $cp => $na) {
        $return["result"][] = $cp;
    }
}

return $return;


#EOF
