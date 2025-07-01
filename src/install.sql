CREATE TABLE IF NOT EXISTS rate_limit (
    ip VARCHAR(42),
    timerange INT UNSIGNED,
    last_seen TIMESTAMP,
    hits INT UNSIGNED,
    UNIQUE(ip, timerange)
);
