<?php

namespace Codepoints\Unicode;

use \Codepoints\Database;
use \Codepoints\Unicode\Block;


/**
 * a Unicode plane, consisting of several Blocks
 */
class Plane {

    /**
     * the plane's name
     *
     * @readonly
     */
    private string $name;

    private int $first;
    private int $last;

    /**
     * the database from which CP info is fetched
     *
     * @readonly
     */
    private Database $db;
    private ?Array $blocks = null;

    /**
     * The previous plane (if any)
     *
     * @var self|false|null
     */
    private $prev = null;

    /**
     * The next plane (if any)
     *
     * @var self|false|null
     */
    private $next = null;

    /**
     * create a new plane instance
     *
     * @param Array{name: string, first: int, last: int} $data
     */
    public function __construct(Array $data, Database $db) {
        $this->name = $data['name'];
        $this->first = $data['first'];
        $this->last = $data['last'];
        $this->db = $db;
    }

    /**
     * get the plane name
     *
     * @psalm-mutation-free
     */
    public function __toString() : string {
        return $this->name;
    }

    /**
     * access info about this plane
     */
    public function __get(string $name) {
        switch ($name) {
        case 'name':
            return $this->name;
        case 'first':
            return $this->first;
        case 'last':
            return $this->last;
        case 'prev':
            return $this->getPrev();
        case 'next':
            return $this->getNext();
        case 'blocks':
            return $this->getBlocks();
        }
    }

    /**
     * get all blocks belonging to this plane
     */
    private function getBlocks() : Array {
        if ($this->blocks === null) {
            $sets = $this->db->getAll('
                SELECT name, first, last FROM blocks
                WHERE first >= ? AND last <= ?',
                $this->first,
                $this->last);
            $this->blocks = [];
            if (is_array($sets)) {
                foreach ($sets as $data) {
                    $this->blocks[] = new Block($data, $this->db);
                }
            }
        }
        return $this->blocks;
    }

    /**
     * get previous plane or false
     *
     * @return self|false
     */
    private function getPrev() {
        if ($this->prev === null) {
            $this->prev = false;
            $data = $this->db->getOne('SELECT name, first, last
                FROM planes
                WHERE last < ?
                ORDER BY first DESC
                LIMIT 1', $this->first);
            /** @psalm-suppress RiskyTruthyFalsyComparison */
            if ($data) {
                $this->prev = new self($data, $this->db);
            }
        }
        return $this->prev;
    }

    /**
     * get next plane or false
     *
     * @return self|false
     */
    private function getNext() {
        if ($this->next === null) {
            $this->next = false;
            $data = $this->db->getOne('SELECT name, first, last
                FROM planes
                WHERE first > ?
                ORDER BY first ASC
                LIMIT 1', $this->last);
            /** @psalm-suppress RiskyTruthyFalsyComparison */
            if ($data) {
                $this->next = new self($data, $this->db);
            }
        }
        return $this->next;
    }

    /**
     * get plane of a specific code point
     */
    public static function getByCodePoint(int $cp, Database $db) : self {
        $data = $db->getOne('
            SELECT name, first, last FROM planes
             WHERE first <= ? AND last >= ?
             LIMIT 1', $cp, $cp);
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if (! $data) {
            throw new \Exception('No plane contains this code point: ' . $cp);
        }
        return new self($data, $db);
    }

    /**
     * get all defined Unicode planes
     *
     * @return list<self>
     */
    public static function getAll(Database $db) : Array {
        $sets = $db->getAll('SELECT name, first, last FROM planes');
        $planes = [];
        if (is_array($sets)) {
            foreach ($sets as $data) {
                $planes[] = new self($data, $db);
            }
        }
        return $planes;
    }

}
