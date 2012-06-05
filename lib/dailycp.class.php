<?php


/**
 * get a daily Codepoint
 */
class DailyCP {

    protected $b = NULL;

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
            $data = array(
                Codepoint::getCP($data[0], $db),
                $data[1], $data[2]
            );
        }
        return $data;
    }

}


//__END__
