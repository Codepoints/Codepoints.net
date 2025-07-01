<?php

namespace Codepoints\Router;

use \Analog\Analog;
use \Codepoints\Database;
use \Codepoints\Router\RateLimitReached;

/**
 * handle rate limits
 */
class RateLimiter {

    private ?string $ip;

    public function __construct(
            public readonly int $limit,
            public readonly int $timerange,
            private Database $db
    ) {
        $this->ip = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR') ?? null;
        if (! $this->ip) {
            return;
        }
        $this->doLimit();
    }

    private function doLimit() {
        $data = $this->db->getOne('SELECT hits
            FROM rate_limit
            WHERE ip = ? AND timerange = ?', $this->ip, $this->timerange);
        if ($data && $data['hits'] > $this->limit) {
            $this->dbExec(
                'UPDATE rate_limit
                SET
                    hits = hits + 1,
                    last_seen = now()
                WHERE ip = ? AND timerange = ?', $this->ip, $this->timerange);
            throw new RateLimitReached();
        }
        $stm = $this->dbExec(
            'INSERT INTO rate_limit (ip, timerange, last_seen, hits)
            VALUES (?, ?, now(), 1)
            ON DUPLICATE KEY UPDATE
                hits = hits + 1,
                last_seen = now()
            ', $this->ip, $this->timerange);
    }

    private function dbExec($query, ...$params) {
        $statement = $this->db->prepare($query);
        return $statement->execute($params);
    }

    /**
     * clear stale entries again from DB
     *
     * Meant to be run in a cronjob.
     */
    public static function clearStale(Database $db) {
        $db->query('DELETE FROM rate_limit WHERE last_seen < DATE_SUB(now(), INTERVAL timerange SECOND)');
    }

}
