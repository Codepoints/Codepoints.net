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
        $this->doLimit();
    }

    private function doLimit() : void {
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if (! $this->ip) {
            /* without a client IP we cannot proceed here */
            return;
        }

        $data = $this->db->getOne('SELECT hits
            FROM rate_limit
            WHERE ip = ?
            AND timerange = ?
            AND last_seen >= DATE_SUB(now(), INTERVAL timerange SECOND)', $this->ip, $this->timerange);
        /** @psalm-suppress RiskyTruthyFalsyComparison */
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

    private function dbExec(string $query, string|int ...$params) : bool {
        $statement = $this->db->prepare($query);
        if (! $statement) {
            return false;
        }
        return $statement->execute($params);
    }

    /**
     * clear stale entries again from DB
     *
     * Meant to be run in a cronjob.
     */
    public static function clearStale(Database $db) : void {
        $db->query('DELETE FROM rate_limit WHERE last_seen < DATE_SUB(now(), INTERVAL timerange SECOND)');
    }

}
