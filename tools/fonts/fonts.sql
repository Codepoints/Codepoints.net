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
-- insert font data
--
INSERT INTO fonts ("id", name, author, publisher, url, copyright, license)
VALUES
    ('DejaVu',
        'DejaVu', 'Bitstream, DejaVu project', 'DejaVu project', 'http://dejavu-fonts.org/wiki/License', '', 'http://dejavu-fonts.org/wiki/License'),
    ('GentiumPlus',
        'Gentium Plus', 'SIL', 'SIL', 'http://scripts.sil.org/gentium_download', '', 'http://scripts.sil.org/OFL'),
    ('FreeFont',
        'GNU FreeFont', 'GNU', 'GNU', 'https://www.gnu.org/software/freefont/', '', 'https://www.gnu.org/software/freefont/license.html'),
    ('Symbola',
        'Symbola', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('damase',
        'MPH 2B Damase', 'Mark Williamson', 'WAZU JAPAN', 'http://www.wazu.jp/gallery/views/View_MPH2BDamase.html', '', ''),
    ('Aegyptus',
        'Aegyptus', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('Quivira',
        'Quivira', 'Alexander Lange', '', 'http://www.quivira-font.com/', '', 'http://en.quivira-font.com/notes.php'),
    ('Anatolian',
        'Anatolian', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('Analecta',
        'Analecta', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('Musica',
        'Musica', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('Akkadian',
        'Akkadian', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('Aegean',
        'Aegean', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('Maya',
        'Maya', 'George Douros', '', 'http://users.teilar.gr/~g1951d/', 'Fonts in this site are offered free for any use; they may be installed, embedded, opened, edited, modified, regenerated, posted, packaged and redistributed.', ''),
    ('noto',
        'NoTo', 'Google', 'Google', 'https://code.google.com/p/noto/', '', 'http://www.apache.org/licenses/LICENSE-2.0.html'),
    ('STIX',
        'STIX', 'STI Pub', 'STI Pub', 'http://www.stixfonts.org/', '', 'http://scripts.sil.org/OFL'),
    ('HanNom',
        'Han Nom', 'Viet Unicode', 'Viet Unicode', 'http://vietunicode.sourceforge.net/fonts/fonts_hannom.html', '', ''),
    ('BabelStoneHan',
        'BabelStone Han', 'BabelStone', 'BabelStone', 'http://www.babelstone.co.uk/Fonts/', '', 'http://ftp.gnu.org/non-gnu/chinese-fonts-truetype/LICENSE'),
    ('BabelStoneOgham',
        'BabelStone Ogham', 'BabelStone', 'BabelStone', 'http://www.babelstone.co.uk/Fonts/', '', 'http://scripts.sil.org/OFL'),
    ('Junicode',
        'Junicode', 'Peter S. Baker', 'Junicode', 'http://junicode.sourceforge.net/', '', 'http://scripts.sil.org/OFL')
;
