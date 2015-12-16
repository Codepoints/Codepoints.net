<?php


/**
 * the result of a wizard search
 *
 * This is a bit different from the SearchResult, because we can't use
 * the search_index table.
 */
class WizardResult extends SearchResult {

    protected $needConfusables = false;

    /**
     * add a query, check, if we need confusables row
     */
    public function addQuery($field, $value, $op='=', $connector='AND') {
        if ($field === 'confusables') {
            $this->needConfusables = true;
        }
        parent::addQuery($field, $value, $op, $connector);
    }

    /**
     * do the search
     */
    protected function fetchNames($set=null) {
        $query = $this->query;
        if ($set !== null) {
            $this->sanitizeSet($set);
            $this->_set = $set;
        }

        list($search, $params) = $this->_getQuerySQL();
        $fields = 'codepoints.cp cp, na, na1,
             codepoint_image.image AS image,
             blocks.name AS block';
        if ($this->needConfusables) {
            $fields .= ',
            (SELECT COUNT(*)
               FROM codepoint_confusables
              WHERE codepoint_confusables.cp = codepoints.cp
                 OR codepoint_confusables.other = codepoints.cp) confusables';
        }
        $select = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT %s
        FROM codepoints
        LEFT JOIN codepoint_script USING ( cp )
        LEFT JOIN codepoint_alias USING ( cp )
        LEFT JOIN codepoint_abstract USING ( cp )
        LEFT JOIN codepoint_image USING ( cp )
        LEFT JOIN blocks ON blocks.first <= codepoints.cp AND blocks.last >= codepoints.cp
        WHERE %s';

        $stm = $this->db->prepare(sprintf($select, $fields, $search.' LIMIT '.($this->page * $this->pageLength).','.$this->pageLength));
        $stm->execute($params);
        $r = $stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();

        $names = array();
        $this->count = 0;

        if ($r !== false) {
            $stm = $this->db->prepare('SELECT FOUND_ROWS() AS c');
            $stm->execute();
            $r2 = $stm->fetch(PDO::FETCH_ASSOC);
            $stm->closeCursor();
            $this->count = (int)$r2['c'];

            foreach ($r as $cp) {
                if (! $cp['image']) {
                    $cp['image'] = '';
                }
                $names[intval($cp['cp'])] = Codepoint::getCP(intval($cp['cp']),
                    $this->db, array('name' => $cp['na']? $cp['na'] : ($cp['na1']? $cp['na1'].'*' : '<control>'),
                    'image' => 'data:image/png;base64,' . $cp['image']));
            }
        }
        $this->set = $names;

        return $this->set;
    }

    /**
     * generate the SQL needed for the query
     */
    protected function _getQuerySQL() {
        $params = array();
        $search = '';
        if (count($this->query) === 0) {
            /* if there are no queries defined but a set to look into, use this.
             * This is basically to ensure compatibility with UnicodeRange. */
            $search = "codepoints.cp IN (" . join(',', $this->_set) . ")";
        } else {
            foreach ($this->query as $i => $q) {
                if ($search !== '') {
                    /* join the query with the last one using the $q3 parameter
                     * (usually "AND" or "OR"). */
                    $search .= " ${q[3]} ";
                }
                if (is_array($q[2])) {
                    /* the query value is an array: we construct an "IN ()" SQL
                     * statement. */
                    $tmp = array_map(array($this->db, 'quote'), $q[2]);
                    if ($q[1] === '=') {
                        $q[1] = 'IN';
                    } elseif ($q[1] === '!=') {
                        $q[1] = 'NOT IN';
                    }
                    if ($q[0] === 'cp') {
                        $search .= " codepoints.cp ${q[1]} ( " . join(',', $tmp) . " )";
                    } elseif ($q[0] === 'block') {
                        $search .= " blocks.name ${q[1]} ( " . join(',', $tmp) . " )";
                    } else {
                        $search .= " `${q[0]}` ${q[1]} ( " . join(',', $tmp) . " )";
                    }
                } elseif ($q[0] === 'na' || $q[0] === 'na1') {
                    /* match names loosely, especially to make the search case-insensiitve */
                    $search .= " `${q[0]}` LIKE :q$i ";
                    $params[':q'.$i] = "%${q[2]}%";
                } elseif ($q[0] === 'cp' || $q[0] === 'int') {
                    /* handle "cp" specially to fight "ambiguous column" SQLite errors */
                    $search .= " codepoints.cp = :q$i ";
                    $params[':q'.$i] = $q[2];
                } elseif ($q[0] === 'block') {
                    $search .= " blocks.name = :q$i ";
                    $params[':q'.$i] = $q[2];
                } else {
                    /* the default is to query the column $q0 with the comparison $q1
                     * for a value $q2 */
                    $search .= " `${q[0]}` ${q[1]} :q$i ";
                    $params[':q'.$i] = $q[2];
                }
            }
        }
        return array($search, $params);
    }

}


//__END__
