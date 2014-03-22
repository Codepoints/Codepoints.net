--
-- set up font table
--
CREATE TABLE IF NOT EXISTS fonts (
    "id"      TEXT PRIMARY KEY,
    name      TEXT,
    author    TEXT,
    publisher TEXT,
    url       TEXT,
    copyright TEXT,
    license   TEXT
);


--
-- set up mapping table
--
CREATE TABLE IF NOT EXISTS codepoint_fonts (
    cp      INTEGER(7) REFERENCES codepoints,
    font    TEXT,
    id      TEXT, -- the font ID used as filename
    primary INTEGER(1) DEFAULT 0, -- used for rendering
    PRIMARY KEY (cp, font)
);
CREATE INDEX IF NOT EXISTS codepoint_fonts_cp ON codepoint_fonts ( cp );
CREATE INDEX IF NOT EXISTS codepoint_fonts_cp_primary ON codepoint_fonts ( cp, primary );


--
-- insert font data
--
INSERT OR REPLACE INTO fonts
    ("id",
        name, author, publisher, url, copyright, license)
VALUES
    ('dejavu',
        'DejaVu', 'Bitstream, DejaVu project', 'DejaVu project', 'http://dejavu-fonts.org/wiki/License', '', 'http://dejavu-fonts.org/wiki/License'),
    ('gentiumplus',
        'Gentium Plus', 'SIL', 'SIL', 'http://scripts.sil.org/gentium_download', '', 'http://scripts.sil.org/OFL'),
    ('freefont',
        'GNU FreeFont', 'GNU', 'GNU', 'https://www.gnu.org/software/freefont/', '', 'https://www.gnu.org/software/freefont/license.html'),
    ('symbola',
        'Symbola', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('damase',
        'MPH 2B Damase', 'Mark Williamson', 'WAZU JAPAN', 'http://www.wazu.jp/gallery/views/View_MPH2BDamase.html', '', ''),
    ('aegyptus',
        'Aegyptus', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('quivira',
        'Quivira', 'Alexander Lange', '', 'http://www.quivira-font.com/', '', 'http://en.quivira-font.com/notes.php'),
    ('anatolian',
        'Anatolian', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('analecta',
        'Analecta', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('musica',
        'Musica', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('akkadian',
        'Akkadian', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('aegean',
        'Aegean', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('maya',
        'Maya', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('noto',
        'NoTo', 'Google', 'Google', 'https://code.google.com/p/noto/', '', 'http://www.apache.org/licenses/LICENSE-2.0.html'),
    ('stix',
        'STIX', 'STI Pub', 'STI Pub', 'http://www.stixfonts.org/', '', 'http://scripts.sil.org/OFL'),
    ('hannom',
        'Han Nom', 'Viet Unicode', 'Viet Unicode', 'http://vietunicode.sourceforge.net/fonts/fonts_hannom.html', '', ''),
    ('babelstonehan',
        'BabelStone Han', 'BabelStone', 'BabelStone', 'http://www.babelstone.co.uk/Fonts/', '', 'http://ftp.gnu.org/non-gnu/chinese-fonts-truetype/LICENSE'),
    ('babelstoneogham',
        'BabelStone Ogham', 'BabelStone', 'BabelStone', 'http://www.babelstone.co.uk/Fonts/', '', 'http://scripts.sil.org/OFL'),
    ('junicode',
        'Junicode', 'Peter S. Baker', 'Junicode', 'http://junicode.sourceforge.net/', '', 'http://scripts.sil.org/OFL'),
    ('myanmar3',
        'MyanMar NLP 3', 'MyanMar NLP', 'MyanMar NLP', 'http://code.google.com/p/myanmar3source/', '', 'http://creativecommons.org/licenses/by/3.0/'),
    ('sourcesans',
        'Source Sans Pro', 'Adobe', 'Adobe', 'https://github.com/adobe/source-sans-pro', '', 'http://scripts.sil.org/OFL'),
    ('sourcecode',
        'Source Code Pro', 'Adobe', 'Adobe', 'https://github.com/adobe/source-code-pro', '', 'http://scripts.sil.org/OFL')
;
