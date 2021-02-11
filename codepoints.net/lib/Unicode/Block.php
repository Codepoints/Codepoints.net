<?php

namespace Codepoints\Unicode;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\Range;


/**
 * A block of characters as defined by Unicode
 */
class Block extends Range {

    /**
     * the block's name
     */
    private $name;

    /**
     * the first and last code point of this block, may be
     * non-existing
     */
    protected $block_limits;

    /**
     * the previous block
     */
    private $prev = null;

    /**
     * the next block
     */
    private $next = null;

    private static Array $instance_cache = [];

    /**
     * create a new Block
     */
    public function __construct(Array $data, Database $db) {
        $this->name = $data['name'];
        $this->block_limits = [$data['first'], $data['last']];
        parent::__construct(range($data['first'], $data['last']), $db);
    }

    /**
     * get the block's official name
     */
    public function __toString() {
        return $this->name;
    }

    /**
     * access info about this block
     */
    public function __get($name) {
        switch ($name) {
        case 'name':
            return $this->name;
        case 'first':
            return $this->block_limits[0];
        case 'last':
            return $this->block_limits[1];
        case 'prev':
            return $this->getPrev();
        case 'next':
            return $this->getNext();
        case 'plane':
            return Plane::getByBlock($this, $this->db);
        }
    }

    /**
     * return the count of codepoints in this block
     */
    public function count() : int {
        $data = $this->db->getOne('SELECT COUNT(*) c
            FROM codepoints
            WHERE cp >= ? AND cp <= ?', $this->first, $this->last);
        return $data['c'];
    }

    /**
     * get the previous block or false
     */
    private function getPrev() {
        if ($this->prev === null) {
            $this->prev = false;
            $data = $this->db->getOne('
                SELECT name, first, last FROM blocks
                WHERE last < ?
                ORDER BY last DESC
                LIMIT 1', $this->first);
            if ($data) {
                $this->prev = new static($data, $this->db);
            }
        }
        return $this->prev;
    }

    /**
     * get the next block or false
     */
    private function getNext() {
        if ($this->next === null) {
            $this->next = false;
            $data = $this->db->getOne('
                SELECT name, first, last FROM blocks
                WHERE first > ?
                ORDER BY first ASC
                LIMIT 1', $this->last);
            if ($data) {
                $this->next = new static($data, $this->db);
            }
        }
        return $this->next;
    }

    /**
     * static function: get a cached Block instance, if it exists
     */
    public static function getCached(Array $data, Database $db) : self {
        if (! array_key_exists($data['first'], self::$instance_cache)) {
            self::$instance_cache[$data['first']] = new self($data, $db);
        }
        return self::$instance_cache[$data['first']];
    }

    /**
     * static function: get the Block for a given code point
     */
    public static function getByCodepoint(Codepoint $codepoint, Database $db) : self {
        $data = $db->getOne('
            SELECT name, first, last FROM blocks
             WHERE first <= ? AND last >= ?
             LIMIT 1', $codepoint->id, $codepoint->id);
        return self::getCached($data, $db);
    }

}
