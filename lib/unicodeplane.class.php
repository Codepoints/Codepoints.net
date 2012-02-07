<?php


class UnicodePlane {

    public $name;
    public $first;
    public $last;
    protected $db;
    protected $blocks;
    protected static $type = 'plane';

    public function __construct($name, $db, $r=NULL) {
        $this->db = $db;
        if ($r === NULL) {
            $query = $this->db->prepare("
                SELECT name, first, last FROM blocks
                WHERE replace(replace(lower(name), '_', ''), ' ', '') = :name
                AND `type` = :type
                LIMIT 1");
                $query->execute(array(':type' => self::$type,
                    ':name' => str_replace(array(' ', '_'), '',
                                        strtolower($name))));
            $r = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();
            if ($r === False) {
                throw new Exception('No plane named ' . $name);
            }
        }
        $this->name = $r['name'];
        $this->first = $r['first'];
        $this->last = $r['last'];
    }

    public function getName() {
        return $this->name;
    }

    public function getBlocks() {
        if ($this->blocks === NULL) {
        $query = $this->db->prepare("
            SELECT name, first, last FROM blocks
             WHERE first >= :first AND last <= :last
               AND `type` = 'block'");
        $query->execute(array(':first' => $this->first,
                              ':last' => $this->last));
        $r = $query->fetchAll(PDO::FETCH_ASSOC);
        $query->closeCursor();
        if ($r === False) {
            $this->blocks = array();
        }
            $this->blocks = $r;
        }
        return $this->blocks;
    }

    public static function getForCodepoint($cp, $db=NULL) {
        if ($cp instanceof Codepoint) {
            $db = $cp->getDB();
            $cp = $cp->getId();
        }
        $query = $db->prepare("
            SELECT name, first, last FROM blocks
             WHERE first <= :cp AND last >= :cp
               AND `type` = :type
             LIMIT 1");
        $query->execute(array(':type' => self::$type, ':cp' => $cp));
        $r = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        if ($r === False) {
            throw new Exception('No plane contains this codepoint: ' . $cp);
        }
        return new self('', $db, $r);
    }

}


//__END__
