#!/usr/bin/python
"""Extract glyphs from a font and store them in the database"""


import fontforge
import os
import sqlite3
import sys


tpl = 'INSERT OR REPLACE INTO codepoint_fonts (cp, font, id) VALUES (?, ?, ?);'
create_statement = """CREATE TABLE IF NOT EXISTS codepoint_fonts (
    cp      INTEGER(7) REFERENCES codepoints,
    font    TEXT,
    id      TEXT, -- the font ID used as filename
    PRIMARY KEY (cp, font)
);"""

def main(args):
    conn = sqlite3.connect('../ucd.sqlite')
    cur = conn.cursor()

    debug = False
    if args[0] == '-n':
        args.pop(0)
        debug = True

    if debug:
        print create_statement
    else:
        cur.execute(create_statement)

    for font in args:
        if not os.path.isfile(font):
            raise ValueError("Is no file: %s" % font)
        _font = fontforge.open(font)
        id = os.path.splitext(os.path.basename(font))[0]
        selection = _font.selection.all().byGlyphs
        name = _font.fullname
        for glyph in selection:
            cp = glyph.unicode
            if (cp > -1 and cp < 0x110000 and not (cp >= 57344 and cp <= 63743)
                and not (cp >= 983040 and cp <= 1114111)):
                if debug:
                    print tpl.replace('?', '%s') % (cp, '"'+name+'"', '"'+id+'"')
                else:
                    cur.execute(tpl, (cp, name, id))

    conn.commit()
    cur.close()
    conn.close()


if __name__ == "__main__":
    main(sys.argv[1:])
