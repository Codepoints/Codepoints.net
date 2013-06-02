<?php


/**
 * get a daily Codepoint
 */
class DailyCP {

    protected $b = NULL;
    protected $db;

    /**
     * construct with DB instance
     */
    public function __construct($db=NULL) {
        $this->db = $db;
    }

    /**
     * fetch the date's codepoint from the db
     */
    protected function _getDataset($date) {
        $q = $this->db->prepare('SELECT cp, comment FROM dailycp
                                  WHERE "date" = ?');
        $q->execute(array($date));
        $data = $q->fetch(PDO::FETCH_NUM);
        if (! $data) {
            $data = NULL;
        }
        return $data;
    }

    /**
     * get the codepoint and additional data for a date
     */
    public function get($date, $db) {
        $this->db = $db;
        $data = self::_getDataset($date);
        if ($data) {
            $data = array(Codepoint::getCP($data[0], $db), $data[1]);
        }
        return $data;
    }

    /**
     * get several CPs for the newsfeed
     */
    public function getSome($limit=100) {
        $q = $this->db->prepare('SELECT cp, comment, date FROM dailycp
                                  WHERE date("date") <= date(?)
                               ORDER BY "date" DESC LIMIT 0,?');
        $q->execute(array(date('Y-m-d'), $limit));
        $data = $q->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

}


//__END__
