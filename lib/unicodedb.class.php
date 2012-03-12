<?php


/**
 * Handle access to the Unicode database
 */
class UnicodeDB {

    private $db;
    private $properties;

    /**
     * Construct with PDO object of database
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Get all possible Unicode properties and their name
     */
    public function getProperties() {
        if ($this->properties === NULL) {
            $query = $this->db->prepare("
                SELECT abbr, name
                  FROM propval
                 WHERE prop = 'prop'");
            $query->execute();
            $p = array();
            while ($r = $query->fetch(PDO::FETCH_ASSOC)) {
                $p[$r['abbr']] = $r['name'];
            }
            $this->properties = $p;
            $query->closeCursor();
        }
        return $this->properties;
    }

}


//__END__
