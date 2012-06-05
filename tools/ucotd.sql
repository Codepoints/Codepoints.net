CREATE TABLE IF NOT EXISTS dailycp (
    `date`   TEXT(10) PRIMARY KEY,
    cp       INTEGER(7) REFERENCES codepoints,
    comment  TEXT
);
