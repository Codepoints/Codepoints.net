CREATE TABLE IF NOT EXISTS codepoint_fonts (
    cp      INTEGER(7) REFERENCES codepoints,
    font    TEXT,
    id      TEXT, -- the font ID used as filename
    PRIMARY KEY (cp, font)
);
