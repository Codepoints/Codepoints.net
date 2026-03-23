<?php

namespace Codepoints\Unicode;

use \JsonSerializable;
use \Codepoints\Unicode\Block;
use \Codepoints\Database;


/**
 * a single Unicode code point
 * @property-read Codepoint|false $prev
 * @property-read Codepoint|false $next
 * @property-read Block $block
 * @property-read Plane $plane
 * @property-read mixed $aliases
 * @property-read mixed $cldr
 * @property-read mixed $confusables
 * @property-read mixed $csur
 * @property-read mixed $description
 * @property-read mixed $extra
 * @property-read mixed $image
 * @property-read mixed $othersites
 * @property-read mixed $pronunciation
 * @property-read mixed $properties
 * @property-read mixed $relatives
 * @property-read mixed $representation
 * @property-read mixed $sensitivity
 * @property-read ?Array{abstract: string, lang: string, src: string} $wikipedia
 */
final class Codepoint implements JsonSerializable {

    /**
     * the Unicode code point as integer
     */
    public readonly int $id;

    /**
     * the official name
     *
     * Possibly some other value, like na1 field or a correction from the
     * aliases list.
     */
    public readonly string $name;

    /**
     * the Unicode General Category
     *
     * Needed for rendering purposes ("is it a control char?", "is it a
     * combining char?")
     */
    public readonly string $gc;

    /**
     * @readonly
     */
    private Database $db;

    /**
     * the previous code point or false for U+0000
     *
     * @var self|false|null
     */
    private $_prev = null;

    /**
     * the next code point or false for U+10FFFF
     *
     * @var self|false|null
     */
    private $_next = null;

    /**
     * @var Array<int, self>
     */
    private static Array $instance_cache = [];

    /**
     * registered info providers. Callables, but mostly instances of CodepointInfo
     *
     * @var Array<string, callable>
     */
    private static Array $info_providers = [];

    /**
     * cached responses of info providers
     *
     * @var Array<string, mixed>
     */
    private Array $info_cache = [];

    /**
     * lazily create a new code point instance
     *
     * The constructor should never be called directly. Use
     * Codepoint::getCached() instead.
     *
     * @param Array{cp: int, name: string, gc: string} $data
     */
    private function __construct(Array $data, Database $db) {
        $this->id = $data['cp'];
        $this->name = $data['name'];
        $this->gc = $data['gc'];
        $this->db = $db;
    }

    /**
     * get the official Unicode ID in the form U+[hex]{4,6}
     *
     * @psalm-mutation-free
     */
    public function __toString() : string {
        return sprintf('U+%04X', $this->id);
    }

    /**
     * allow read access to our data
     *
     * @return mixed
     */
    public function __get(string $name) {
        switch ($name) {
        case 'prev':
            return $this->getPrev();
        case 'next':
            return $this->getNext();
        case 'block':
            return Block::getByCodePoint($this->id, $this->db);
        case 'plane':
            return Plane::getByCodePoint($this->id, $this->db);
        default:
            return $this->getInfo($name);
        }
    }

    /**
     * get a safe, printable representation
     *
     * Some control characters have dedicated representations. We use those,
     * e.g., U+0000 => U+2400.
     * Combining characters are accompanied by U+25CC Dotted Circle.
     *
     * @psalm-mutation-free
     */
    public function chr() : string {
        if (in_array($this->gc, ['Mn', 'Me', 'Lm', 'Sk'])) {
            return (string)mb_chr(0x25CC) . (string)mb_chr($this->id);
        }
        return (string)mb_chr(get_printable_codepoint($this->id, $this->gc));
    }

    /**
     * simplest possible JSON serialization: get the code point
     *
     * @psalm-mutation-free
     */
    #[\Override]
    public function jsonSerialize(): int {
        return $this->id;
    }

    /**
     * get a bit of information about this code point from a registered
     * info source
     *
     * @see self::addInfoProvider
     * @return mixed
     */
    private function getInfo(string $name) {
        if (! array_key_exists($name, $this->info_cache)) {
            $this->info_cache[$name] = null;
            if (array_key_exists($name, static::$info_providers)) {
                $this->info_cache[$name] = static::$info_providers[$name]($this);
            }
        }
        return $this->info_cache[$name];
    }

    /**
     * get the previous codepoint
     *
     * @return self|false
     */
    private function getPrev() {
        if ($this->_prev === null) {
            $this->_prev = false;
            $other = $this->db->getOne('SELECT cp, name, gc FROM codepoints
                WHERE cp < ?
                ORDER BY cp DESC
                LIMIT 1', $this->id);
            /** @psalm-suppress RiskyTruthyFalsyComparison */
            if ($other) {
                $this->_prev = self::getCached($other, $this->db);
            }
        }
        return $this->_prev;
    }

    /**
     * get the next codepoint
     *
     * @return self|false
     */
    private function getNext() {
        if ($this->_next === null) {
            $this->_next = false;
            $other = $this->db->getOne('SELECT cp, name, gc FROM codepoints
                WHERE cp > ?
                ORDER BY cp ASC
                LIMIT 1', $this->id);
            /** @psalm-suppress RiskyTruthyFalsyComparison */
            if ($other) {
                $this->_next = self::getCached($other, $this->db);
            }
        }
        return $this->_next;
    }

    /**
     * static function: get a cached Codepoint instance, if it exists
     */
    public static function getCached(Array $data, Database $db) : self {
        $cp = $data['cp'];
        if (! array_key_exists($cp, self::$instance_cache)) {
            self::$instance_cache[$cp] = new self($data, $db);
        }
        return self::$instance_cache[$cp];
    }

    /**
     * register an information provider class
     */
    public static function addInfoProvider(string $name, callable $provider) : void {
        static::$info_providers[$name] = $provider;
    }

    /**
     * check the registration state of an information provider class
     */
    public static function hasInfoProvider(string $name) : bool {
        return array_key_exists($name, static::$info_providers);
    }

}
