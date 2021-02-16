<?php

namespace Codepoints\Unicode;

use \Analog\Analog;
use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\Range;


/**
 * A block of characters as defined by Unicode
 */
class Block extends Range {

    /**
     * the previous block
     *
     * @var self|bool|null
     */
    private $prev = null;

    /**
     * the next block
     *
     * @var self|bool|null
     */
    private $next = null;

    /**
     * cache of already fetched blocks
     */
    private static Array $instance_cache = [];

    /**
     * create a new Block
     */
    public function __construct(Array $data, Database $db) {
        parent::__construct($data, $db);
        /* set the name to the canonical Unicode block name. Unicode themselves
         * say, that the block name is nothing more than an alias for a fixed
         * range. */
        $this->name = $data['name'];
    }

    /**
     * access info about this block
     *
     * @return mixed
     */
    public function __get(string $name) {
        switch ($name) {
        case 'prev':
            return $this->getPrev();
        case 'next':
            return $this->getNext();
        case 'plane':
            return Plane::getByCodePoint($this->first, $this->db);
        default:
            return parent::__get($name);
        }
    }

    /**
     * get the previous block or false
     *
     * @return self|bool
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
                $this->prev = new self($data, $this->db);
            }
        }
        return $this->prev;
    }

    /**
     * get the next block or false
     *
     * @return self|bool
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
                $this->next = new self($data, $this->db);
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
    public static function getByCodePoint(int $id, Database $db) : self {
        $data = $db->getOne('
            SELECT name, first, last FROM blocks
             WHERE first <= ? AND last >= ?
             LIMIT 1', $id, $id);
        if (! $data) {
            throw new \Exception('no block contains this code point: '.$id);
        }
        return self::getCached($data, $db);
    }

}
