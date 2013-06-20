#!/usr/bin/python


import fontforge
import os
import sqlite3
import subprocess
import sys
from base64 import b64encode


def create_imgs(args):
    rm_result = True
    font = args[0]
    if not os.path.isfile(font):
        raise ValueError("Is no file: %s" % font)
    folder = os.path.splitext(os.path.basename(font))[0]
    if not os.path.isdir(folder):
        os.mkdir(folder)
    sql = open(folder+'_insert.sql', 'wb')
    selection = fontforge.open(font).selection.all().byGlyphs
    for cp in xrange(0x13000, 0x1342E):
        xcp = '%04X' % cp
        fn = "%s/U+%s.png" % (folder, xcp)
        tfn = "%s/U+%s.tmp.png" % (folder, xcp)
        subprocess.call([
            "convert", "-background", "none", "-gravity", "center",
            "-size", "16x16", "-fill", "black", "-font", font,
            "-pointsize", "16", "label:"+unichr(cp).encode("UTF-8"), tfn])
        subprocess.call(["pngcrush", "-q", "-rem", "alla", tfn, fn])
        subprocess.call(["rm", tfn])
        fo = open(fn)
        sql.write("INSERT OR REPLACE INTO codepoint_image VALUES ( %s, '%s');\n" %
                (cp, b64encode(fo.read())))
        fo.close()
        if rm_result:
            subprocess.call(["rm", fn])


tpl = 'INSERT OR REPLACE INTO codepoint_fonts (cp, font, id) VALUES (?, ?, ?);'
create_statement = """CREATE TABLE IF NOT EXISTS codepoint_fonts (
    cp      INTEGER(7) REFERENCES codepoints,
    font    TEXT,
    id      TEXT, -- the font ID used as filename
    PRIMARY KEY (cp, font)
);"""

def create_fontface(args):
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
        for cp in xrange(0x13000, 0x1342E):
            if debug:
                print tpl.replace('?', '%s') % (cp, '"'+name+'"', '"'+id+'"')
            else:
                cur.execute(tpl, (cp, name, id))

    conn.commit()
    cur.close()
    conn.close()


def main(args):
    #create_imgs(args)
    create_fontface(args)


if __name__ == "__main__":
    main(sys.argv[1:])
