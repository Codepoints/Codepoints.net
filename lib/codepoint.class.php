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

    /**
     * access db for dependency injection
     */
    public function getDB() {
        return $this->db;
    }

    /**
     * get the character representation in a specific encoding
     */
    public function getChar($coding='UTF-8') {
        return mb_convert_encoding('&#'.$this->id.';', $coding,
                                   'HTML-ENTITIES');
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
            $props = $this->getProperties();
            $r = $props['image'];
            if (! $r) {
                $r = 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQAAAAA3iMLMAAAAIUlE'.
                     'QVQI12P4/58Bghr/M8z8z7AUjFr/M3SCUSMSA6wMAI8sGvs6OjZh'.
                     'AAAAAElFTkSuQmCC';
            }
            $this->image = 'image/png;base64,' . $r;
        }
        return $this->image;
    }

    /**
     * fetch name
     */
    public function getName() {
        if ($this->name === NULL) {
            $props = $this->getProperties();
            if ($props === False) {
                throw new Exception('This Codepoint doesnt exist: '.$this->id);
            } else {
                if (isset($props['na']) && $props['na']) {
                    $this->name = $props['na'];
                } elseif (isset($props['na1']) && $props['na1']) {
                    $this->name = $props['na1'].'*';
                } else {
                    $this->name = '<control>';
                }
            }
        }
        return $this->name;
    }

    /**
     * fetch its properties
     */
    public function getProperties() {
        if ($this->properties === NULL) {
            $query = $this->db->prepare('
                SELECT *, (SELECT codepoint_image.image
                             FROM codepoint_image
                            WHERE codepoint_image.cp = codepoints.cp) image,
                          (SELECT codepoint_script.sc
                             FROM codepoint_script
                            WHERE codepoint_script.cp = codepoints.cp) sc
                FROM codepoints
                WHERE cp = :cp LIMIT 1');
            $query->execute(array(':cp' => $this->id));
            $codepoint = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();
            $this->properties = $codepoint;
            $query = $this->db->prepare('SELECT *
                                           FROM codepoint_relation
                                          WHERE cp = :cp');
            $query->execute(array(':cp' => $this->id));
            $rel = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();
            foreach ($rel as $v) {
                if ($v['order'] == 0) {
                    $this->properties[$v['relation']] = new Codepoint($v['other'], $this->db);
                } else {
                    if (! array_key_exists($v['relation'], $this->properties)) {
                        $this->properties[$v['relation']] = array();
                    }
                    $this->properties[$v['relation']][$v['order'] - 1] = new Codepoint($v['other'], $this->db);
                }
            }
        }
        return $this->properties;
    }

    /**
     * fetch one property, possibly already as new self
     */
    public function getProp($prop, $default=NULL) {
        $props = $this->getProperties();
        if (array_key_exists($prop, $props)) {
            if (in_array($prop, array('bmg','suc','slc','stc','scf'))) {
                if (intval($v) === $this->id) {
                    return $this;
                } else {
                    return new Codepoint(intval($props[$prop]), $this->db);
                }
            } elseif (in_array($prop, array('uc','lc','tc','cf','dm',
                                            'FC_NFKC','NFKC_CF'))) {
                $r = array();
                $s = explode(' ', $props[$prop]);
                foreach ($s as $v) {
                    if (intval($v) === $this->id) {
                        $r[] = $this;
                    } else {
                        $r[] = new Codepoint(intval($v), $this->db);
                    }
                }
                return $r;
            } elseif (in_array($prop, array(
                'Bidi_M', 'Bidi_C', 'CE', 'Comp_Ex', 'XO_NFC', 'XO_NFD', 'XO_NFKC',
                'XO_NFKD', 'Join_C', 'Upper', 'Lower', 'OUpper', 'OLower', 'CI', 'Cased',
                'CWCF', 'CWCM', 'CWL', 'CWKCF', 'CWT', 'CWU', 'IDS', 'OIDS', 'XIDS',
                'IDC', 'OIDC', 'XIDC', 'Pat_Syn', 'Pat_WS', 'Dash', 'Hyphen', 'QMark',
                'Term', 'STerm', 'Dia', 'Ext', 'SD', 'Alpha', 'OAlpha', 'Math', 'OMath',
                'Hex', 'AHex', 'DI', 'ODI', 'LOE', 'WSpace', 'Gr_Base', 'Gr_Ext',
                'OGr_Ext', 'Gr_Link', 'Ideo', 'UIdeo', 'IDSB', 'IDST', 'Radical', 'Dep',
                'VS', 'NChar'))) {
                if ($props[$prop] === '1') {
                    return true;
                } elseif ($props[$prop] === '0') {
                    return false;
                } else {
                    return null;
            }
            } else {
                return $props[$prop];
            }
        }
        return $default;
    }

    /**
     * get pronounciation of a codepoint (Pinyin)
     */
    public function getPronunciation() {
        $props = $this->getProperties();
        $pr = '';
        $toPinyin = false;
        if ($props['kHanyuPinlu']) {
            $toPinyin = true;
            $pr = preg_replace('/^([a-z0-9]+).*/', '$1',
                               $props['kHanyuPinlu']);
        }
        if (! $pr && $props['kXHC1983']) {
            $pr = preg_replace('/^[0-9.*,]+:([^ ,]+)(?:[ ,].*)?$/', '$1',
                               $props['kXHC1983']);
        }
        if (! $pr && $props['kHanyuPinyin']) {
            $pr = preg_replace('/^[0-9.*,]+:([^ ,]+)(?:[ ,].*)?$/', '$1',
                               $props['kHanyuPinyin']);
        }
        if (! $pr && $props['kMandarin']) {
            $toPinyin = true;
            $pr = strtolower(preg_replace('/^([A-Z0-9]+).*/', '$1',
                               $props['kMandarin']));
        }
        if ($toPinyin) {
            $pr = preg_replace_callback('/([aeiouü])([^aeiouü12345]*)([12345])/',
                function($matches) {
                    $map = array(
                        'a' => array(1 => '0101', 2 => '00E1', 3 => '01CE', 4 => '00E0'),
                        'e' => array(1 => '0113', 2 => '00E9', 3 => '011B', 4 => '00E8'),
                        'i' => array(1 => '012B', 2 => '00ED', 3 => '01D0', 4 => '00EC'),
                        'o' => array(1 => '014D', 2 => '00F3', 3 => '01D2', 4 => '00F2'),
                        'u' => array(1 => '016B', 2 => '00FA', 3 => '01D4', 4 => '00F9'),
                        'ü' => array(1 => '01D6', 2 => '01D8', 3 => '01DA', 4 => '01DC'),
                    );
                    if (array_key_exists($matches[1], $map) &&
                        array_key_exists($matches[3], $map[$matches[1]])) {
                        $mod = mb_convert_encoding('&#x'.$map[$matches[1]][$matches[3]].';', 'UTF-8',
                                'HTML-ENTITIES');
                    } else {
                        $mod = $matches[1];
                    }
                    return $mod.$matches[2];
                }, $pr);
        }
        return $pr;
    }

    /**
     * get the containing Unicode Block
     */
    public function getBlock() {
        if ($this->block === NULL) {
            $this->block = UnicodeBlock::getForCodepoint($this);
        }
        return $this->block;
    }

    /**
     * get the containing Unicode Plane
     */
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

    /**
     * get the previous codepoint (or False)
     */
    public function getPrev() {
        if ($this->prev === NULL) {
            $query = $this->db->prepare('SELECT cp FROM codepoints
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

    /**
     * get the next codepoint (or False)
     */
    public function getNext() {
        if ($this->next === NULL) {
            $query = $this->db->prepare('SELECT cp FROM codepoints
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

    /**
     * search database by name
     */
    public static function getByName($name, $db) {
        $query = $db->prepare("
            SELECT * FROM codepoints
            WHERE replace(replace(lower(na), '_', ''), ' ', '') = :name
               OR replace(replace(lower(na1), '_', ''), ' ', '') = :name
            LIMIT 1");
        $query->execute(array(':name' => str_replace(array(' ', '_'), '',
                                strtolower($name))));
        $r = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        if ($r === False) {
            throw new Exception('No codepoint named ' . $name);
        }
        return new self($r['cp'], $db, array(
            'properties' => $r
        ));
    }

}


//__END__
