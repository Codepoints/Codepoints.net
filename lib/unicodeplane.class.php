<?php


class UnicodePlane {

    public $name;
    public $first;
    public $last;
    protected $db;
    protected $blocks;
    protected $prev;
    protected $next;

    public function __construct($name, $db, $r=NULL) {
        $this->db = $db;
        if ($r === NULL) {
            $query = $this->db->prepare("
                SELECT name, first, last FROM planes
                WHERE replace(replace(lower(name), '_', ''), ' ', '') = :name
                LIMIT 1");
                $query->execute(array(':name' => str_replace(array(' ', '_'), '',
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
             WHERE first >= :first AND last <= :last");
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

    public function getPrev() {
        if ($this->prev === NULL) {
            $query = $this->db->prepare('SELECT name, first, last
                FROM planes
                WHERE last < ?
                ORDER BY first DESC
                LIMIT 1');
            $query->execute(array($this->first));
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
            $query = $this->db->prepare('SELECT name, first, last
                FROM planes
                WHERE first > ?
                ORDER BY first ASC
                LIMIT 1');
            $query->execute(array($this->last));
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

    public static function getForCodepoint($cp, $db=NULL) {
        if ($cp instanceof Codepoint) {
            $db = $cp->getDB();
            $cp = $cp->getId();
        }
        $query = $db->prepare("
            SELECT name, first, last FROM planes
             WHERE first <= :cp AND last >= :cp
             LIMIT 1");
        $query->execute(array(':cp' => $cp));
        $r = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        if ($r === False) {
            throw new Exception('No plane contains this codepoint: ' . $cp);
        }
        return new self('', $db, $r);
    }

    public static function getAll($db) {
        $query = $db->query("SELECT * FROM planes");
        $r = $query->fetchAll(PDO::FETCH_ASSOC);
        $query->closeCursor();
        $planes = array();
        foreach ($r as $pl) {
            $planes[] = new self($pl['name'], $db, $pl);
        }
        return $planes;
    }

}


//__END__
