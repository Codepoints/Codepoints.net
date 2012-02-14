<?php


class Codepoint {

    protected $id;
    protected $db;
    protected $properties;
    protected $name;
    protected $block;
    protected $plane;
    protected $alias;
    protected $prev;
    protected $next;
    protected $image;

    /**
     * Construct with PDO object of database
     *
     * $info can be used to pre-fill data, e.g., when it was bulk-loaded in a
     * block
     */
    public function __construct($id, $db, $info=array()) {
        $this->id = $id;
        $this->db = $db;
        foreach ($info as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * get the ID in various forms
     */
    public function getId($type='dec') {
        switch ($type) {
        case 'hex':
            return self::hex($this->id);
        case 'full':
            return $this->__toString();
        case 'name':
            return $this->getName();
        default:
            return $this->id;
        }
    }

    /**
     * get the official Unicode ID
     */
    public function __toString() {
        return self::hex($this->id);
    }

    public function getDB() {
        return $this->db;
    }

    /**
     * get the character representation in a specific encoding
     */
    public function getChar($coding='UTF-8') {
        return mb_convert_encoding('&#'.$this->id.';', $coding, 'HTML-ENTITIES');
    }

    /**
     * get representation in a certain encoding
     */
    public function getRepr($coding='UTF-8') {
        return join(' ',
            str_split(
                strtoupper(
                    bin2hex($this->getChar($coding))), 2));
    }

    /**
     * get an image representing the character
     */
    public function getImage() {
        if ($this->image === NULL) {
            $q = $this->db->prepare('SELECT data FROM img WHERE id = ?');
            $q->execute(array($this->id));
            $r = $q->fetch();
            if ($r) {
                $r = 'image/png;base64,' . $r[0];
            }
            $this->image = $r;
        }
        return $this->image;
    }

    /**
     * fetch name
     */
    public function getName() {
        if ($this->name === NULL) {
            $props = $this->getProperties();
            if (isset($props['na']) && $props['na']) {
                $this->name = $props['na'];
            } elseif (isset($props['na1']) && $props['na1']) {
                $this->name = $props['na1'].'*';
            } else {
                throw new Exception('This Codepoint doesnt exist: '.$this->cp);
            }
        }
        return $this->name;
    }

    /**
     * fetch its properties
     */
    public function getProperties() {
        if ($this->properties === NULL) {
            $query = $this->db->prepare('SELECT * FROM data
                                        WHERE cp = :cp LIMIT 1');
            $query->execute(array(':cp' => $this->id));
            $codepoint = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();
            $this->properties = $codepoint;
        }
        return $this->properties;
    }

    public function getBlock() {
        if ($this->block === NULL) {
            $this->block = UnicodeBlock::getForCodepoint($this);
        }
        return $this->block;
    }

    public function getPlane() {
        if ($this->plane === NULL) {
            $this->plane = UnicodePlane::getForCodepoint($this);
        }
        return $this->plane;
    }

    /**
     * get a set of alias names for this codepoint
     */
    public function getAlias() {
        if ($this->alias === NULL) {
            $query = $this->db->prepare('SELECT cp, name, `type` FROM alias
                                        WHERE cp = :cp');
            $query->execute(array(':cp' => $this->id));
            $this->alias = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();
        }
        return $this->alias;
    }

    public function getPrev() {
        if ($this->prev === NULL) {
            $query = $this->db->prepare('SELECT cp FROM data
                    WHERE cp < :cp
                    ORDER BY cp DESC
                    LIMIT 1');
            $query->execute(array(':cp' => $this->id));
            $prev = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();
            if ($prev) {
                $this->prev = new Codepoint($prev['cp'], $this->db);
            } else {
                $this->prev = false;
            }
        }
        return $this->prev;
    }

    public function getNext() {
        if ($this->next === NULL) {
            $query = $this->db->prepare('SELECT cp, na, na1 FROM data
                    WHERE cp > :cp
                    ORDER BY cp ASC
                    LIMIT 1');
            $query->execute(array(':cp' => $this->id));
            $next = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();
            if ($next) {
                $this->next = new Codepoint($next['cp'], $this->db);
            } else {
                $this->next = false;
            }
        }
        return $this->next;
    }

    /**
     * int-to-hex with formatting
     */
    public static function hex($int) {
        if ($int === NULL || $int === False) {
            return NULL;
        }
        return sprintf("%04X", $int);
    }

}


//__END__
