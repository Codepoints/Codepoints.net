<?php


/**
 * the result of a Unicode codepoint search
 */
class SearchResult extends UnicodeRange {

    protected $query = array();

    public $pageLength = 128;

    public $page = 0;

    protected $count = 0;

    /**
     * add a query, connect it to previous via $connector
     */
    public function addQuery($field, $value, $op='=', $connector='AND') {
        $this->query[] = array($field, $op, $value, $connector);
    }

    /**
     * get the query list
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * do the search
     */
    protected function fetchNames($set=Null) {
        $query = $this->query;
        if ($set !== Null) {
            $this->sanitizeSet($set);
            $this->_set = $set;
        }

        list($search, $params) = $this->_getQuerySQL();

        $sql = 'SELECT cp, na, na1, (SELECT codepoint_image.image
                                     FROM codepoint_image
                                    WHERE codepoint_image.cp = codepoints.cp) image
                  FROM codepoints
                  JOIN codepoint_script USING ( cp )
                 WHERE ' . $search . '
                 LIMIT '.($this->page * $this->pageLength).','.$this->pageLength;
        $stm = $this->db->prepare($sql);
        $stm->execute($params);
        $r = $stm->fetchAll(PDO::FETCH_ASSOC);
        $names = array();
        $stm->closeCursor();
        if ($r !== False) {
            foreach ($r as $cp) {
                if (! $cp['image']) {
                    $cp['image'] = Codepoint::$defaultImage;
                }
                $names[intval($cp['cp'])] = Codepoint::getCP(intval($cp['cp']),
                    $this->db, array('name' => $cp['na']? $cp['na'] : ($cp['na1']? $cp['na1'].'*' : '<control>'),
                    'image' => 'image/png;base64,' . $cp['image']));
            }
        }
        $this->set = $names;

        // we need to get the count separately, because the above
        // query is LIMITed
        $c = count($this->set);
        if ($this->page > 1 || $c === $this->pageLength) {
            $sql = 'SELECT COUNT(*) as c
                      FROM codepoints
                      JOIN codepoint_script USING ( cp )
                     WHERE ' . $search;
            $stm = $this->db->prepare($sql);
            $stm->execute($params);
            $r = $stm->fetch(PDO::FETCH_ASSOC);
            $stm->closeCursor();
            $c = $r['c'];
        }
        $this->count = $c;

        return $this->set;
    }

    /**
     * get the result count
     */
    public function getCount() {
        $this->_prepare();
        return $this->count;
    }

    /**
     * generate the SQL needed for the query
     */
    protected function _getQuerySQL() {
        $params = array();
        $search = '';
        if (count($this->query) === 0) {
            $search = "cp IN (" . join(',', $this->_set) . ")";
        } else {
            foreach ($this->query as $i => $q) {
                if ($search !== '') {
                    $search .= " ${q[3]} ";
                }
                if (is_array($q[2])) {
                    $tmp = array_map(array($this->db, 'quote'), $q[2]);
                    if ($q[1] === '=') {
                        $q[1] = 'IN';
                    } elseif ($q[1] === '!=') {
                        $q[1] = 'NOT IN';
                    }
                    $search .= " `${q[0]}` ${q[1]} ( " . join(',', $tmp) . " )";
                } elseif ($q[0] === 'na' || $q[0] === 'na1') {
                    $search .= " `${q[0]}` LIKE :q$i ";
                    $params[':q'.$i] = "%${q[2]}%";
                } elseif ($q[0] === 'int') {
                    $search .= " cp = :q$i ";
                    $params[':q'.$i] = $q[2];
                } else {
                    $search .= " `${q[0]}` ${q[1]} :q$i ";
                    $params[':q'.$i] = $q[2];
                }
            }
        }
        return array($search, $params);
    }

}


//__END__
