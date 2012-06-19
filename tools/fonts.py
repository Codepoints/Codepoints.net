#!/usr/bin/python


import fontforge
import os
import sqlite3
import sys


tpl = 'INSERT OR REPLACE INTO codepoint_fonts (cp, font) VALUES (?, ?);'

def main(args):
    conn = sqlite3.connect('../ucd.sqlite')
    cur = conn.cursor()

    debug = False
    if args[0] == '-n':
        args.pop(0)
        debug = True

    if debug:
        print "CREATE TABLE"
    else:
        cur.execute("""CREATE TABLE IF NOT EXISTS codepoint_fonts (
            cp     INTEGER(7) REFERENCES codepoints,
            font   TEXT,
            PRIMARY KEY (cp, font)
        );""")

    for font in args:
        if not os.path.isfile(font):
            raise ValueError("Is no file: %s" % font)
        _font = fontforge.open(font)
        selection = _font.selection.all().byGlyphs
        name = _font.fullname
        for glyph in selection:
            cp = glyph.unicode
            if (cp > -1 and cp < 0x110000 and not (cp >= 57344 and cp <= 63743)
                and not (cp >= 983040 and cp <= 1114111)):
                if debug:
                    print (cp, name)
                else:
                    cur.execute(tpl, (cp, name))

    conn.commit()
    cur.close()
    conn.close()


if __name__ == "__main__":
    main(sys.argv[1:])
