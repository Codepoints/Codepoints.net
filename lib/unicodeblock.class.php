<?php


class UnicodeBlock extends UnicodeRange {

    protected $name;
    protected $db;
    protected $prev;
    protected $next;
    protected $plane;
    protected static $type = 'block';

    public function __construct($name, $db, $r=NULL) {
        $this->db = $db;
        if ($r === NULL) { // performance: allow to specify range
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
                throw new Exception('No block named ' . $name);
            }
        }
        $this->name = $r['name']; // use canonical name
        $this->set = range($r['first'], $r['last']);
    }

    public function getName() {
        return $this->name;
    }

    public function getPrev() {
        if ($this->prev === NULL) {
            $query = $this->db->prepare("
            SELECT name, first, last FROM blocks
             WHERE first < :cp AND last < :cp
               AND `type` = :type
          ORDER BY last DESC
             LIMIT 1");
            $query->execute(array(':type' => self::$type,
                                  ':cp' => $this->set[0]));
            $r = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();
            if ($r === False) {
                $this->prev = False;
            } else {
                $this->prev = new self('', $this->db, $r);
            }
        }
        return $this->prev;
    }

    public function getNext() {
        if ($this->next === NULL) {
            $query = $this->db->prepare("
            SELECT name, first, last FROM blocks
             WHERE first > :cp AND last > :cp
               AND `type` = :type
          ORDER BY first ASC
             LIMIT 1");
            $query->execute(array(':type' => self::$type,
                                  ':cp' => end($this->set)));
            $r = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();
            if ($r === False) {
                $this->next = False;
            } else {
                $this->next = new self('', $this->db, $r);
            }
        }
        return $this->next;
    }

    public function getPlane() {
        if ($this->plane === NULL) {
            $query = $this->db->prepare("
            SELECT name, first, last FROM blocks
             WHERE first <= :first AND last >= :last
               AND `type` = 'plane'
             LIMIT 1");
            $query->execute(array(':first' => $this->set[0],
                                  ':last' => end($this->set)));
            $r = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();
            if ($r === False) {
                $this->plane = False;
            } else {
                $this->plane = new UnicodePlane('', $this->db, $r);
            }
        }
        return $this->plane;
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
            throw new Exception('No block contains this codepoint: ' . $cp);
        }
        return new self($name, $db, $r);
    }

}


//__END__
