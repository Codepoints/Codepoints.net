CREATE TABLE IF NOT EXISTS dailycp (
    `date`   DATE PRIMARY KEY,
    cp       INTEGER(7) REFERENCES codepoints,
    comment  VARCHAR(255)
);
