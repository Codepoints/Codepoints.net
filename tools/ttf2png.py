#!/usr/bin/python


import fontforge as ff
import os
import subprocess
import sys
from base64 import b64encode


def main(args):
    rm_result = True
    font = args[0]
    unknown = open('unknown.list').read()
    if not os.path.isfile(font):
        raise ValueError("Is no file: %s" % font)
    folder = os.path.splitext(os.path.basename(font))[0]
    if not os.path.isdir(folder):
        os.mkdir(folder)
    sql = open(folder+'_insert.sql', 'wb')
    selection = ff.open(font).selection.all().byGlyphs
    for glyph in selection:
        cp = glyph.unicode
        xcp = '%04X' % cp
        if cp > 65535 or "\n"+str(cp)+"\n" in unknown:
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


if __name__ == "__main__":
    main(sys.argv[1:])
