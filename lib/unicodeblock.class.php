<?php


/**
 * A block of characters as defined by Unicode
 */
class UnicodeBlock extends UnicodeRange {

    protected $name;
    protected $prev;
    protected $next;
    protected $plane;
    protected $limits;

    public function __construct($name, $db, $r=NULL) {
        if ($r === NULL) { // performance: allow to specify range
            $query = $db->prepare("
                SELECT name, first, last FROM blocks
                WHERE replace(replace(lower(name), '_', ''), ' ', '') = :name
                LIMIT 1");
            $query->execute(array(':name' => str_replace(array(' ', '_'), '',
                                  strtolower($name))));
            $r = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();
            if ($r === False) {
                throw new Exception('No block named ' . $name);
            }
        }
        $this->name = $r['name']; // use canonical name
        $this->limits = array($r['first'], $r['last']);
        parent::__construct(range($r['first'], $r['last']), $db);
    }

    public function getName() {
        return $this->name;
    }

    public function getLimits() {
        return $this->limits;
    }

    public function getPrev() {
        if ($this->prev === NULL) {
            $query = $this->db->prepare("
            SELECT name, first, last FROM blocks
             WHERE first < :cp AND last < :cp
          ORDER BY last DESC
             LIMIT 1");
            $query->execute(array(':cp' => $this->getFirst()));
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
          ORDER BY first ASC
             LIMIT 1");
            $query->execute(array(':cp' => $this->getLast()));
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
            SELECT name, first, last FROM planes
             WHERE first <= :first AND last >= :last
             LIMIT 1");
            $query->execute(array(':first' => $this->getFirst(),
                                  ':last' => $this->getLast()));
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
             LIMIT 1");
        $query->execute(array(':cp' => $cp));
        $r = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        if ($r === False) {
            throw new Exception('No block contains this codepoint: ' . $cp);
        }
        return new self('', $db, $r);
    }

}


//__END__
