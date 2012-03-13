<?php


/**
 * the result of a Unicode codepoint search
 */
class SearchResult extends UnicodeRange {

    protected $query = array();
    public $pageLength = 128;
    public $page = 0;
    protected $count = 0;

    public function addQuery($field, $value, $op='=') {
        $this->query[] = array($field, $op, $value);
    }

    public function getQuery() {
        return $this->query;
    }

    public function search($query=Null) {
        if (! $query) {
            $query = $this->query;
        }
        $params = array();
        $search = array();
        $sql = 'SELECT cp, na, na1, (SELECT codepoint_image.image
                                     FROM codepoint_image
                                    WHERE codepoint_image.cp = codepoints.cp) image
                FROM codepoints
               WHERE ';
        foreach ($query as $i => $q) {
            $search[] = " `${q[0]}` ${q[1]} :q$i ";
            $params[':q'.$i] = $q[2];
        }
        $sql .= join('AND', $search);
        $sql .= ' LIMIT '.($this->page*$this->pageLength).','.$this->pageLength;
        $stm = $this->db->prepare($sql);
        $stm->execute($params);
        $r = $stm->fetchAll(PDO::FETCH_ASSOC);
        $names = array();
        $stm->closeCursor();
        if ($r !== False) {
            foreach ($r as $cp) {
                $names[intval($cp['cp'])] = new Codepoint(intval($cp['cp']),
                    $this->db, array('name' => $cp['na']? $cp['na'] : ($cp['na1']? $cp['na1'].'*' : '<control>'),
                    'image' => 'image/png;base64,' . $cp['image']));
            }
        }
        $this->set = $names;
        $sql = 'SELECT COUNT(*) as c
                FROM codepoints
               WHERE ';
        foreach ($query as $i => $q) {
            $search[] = " `${q[0]}` ${q[1]} :q$i ";
            $params[':q'.$i] = $q[2];
        }
        $sql .= join('AND', $search);
        $stm = $this->db->prepare($sql);
        $stm->execute($params);
        $r = $stm->fetch(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        $this->count = $r['c'];
        return $this;
    }

    public function getCount() {
        return $this->count;
    }

}


//__END__
