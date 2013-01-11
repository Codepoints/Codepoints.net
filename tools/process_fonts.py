#!/usr/bin/python


import fontforge
import json
import os
import subprocess
import sys
from base64 import b64encode
#from ttfquery import describe, glyphquery


def getFonts():
    fonts = json.load(open('fonts.json'))
    fontlist = [[font, makeFontforgeFont(font)] \
                for font in fonts]
    return fontlist


#def makeTtfqueryFont(filename):
#    """Return a ttfQuery font object"""
#    return describe.openFont(filename)


def makeFontforgeFont(filename):
    """Return a FontForge font object"""
    return fontforge.open(filename)


def fontHasGlyph(fontforgeFont, cp):
    """Check, if the font has a certain glyph"""
    try:
        fontforgeFont[cp]
    except TypeError:
        return False
    else:
        return True


def getFontForCodepoint(fontlist, cp):
    """Get the first font, that contains a given glyph"""
    for font in fontlist:
        if fontHasGlyph(font[1], cp):
            return font
    return None


def getGlyphAsPng(font, cp):
    """"""
    xcp = '%04X' % cp
    fn = "/tmp/U+%s.png" % xcp
    tfn = "/tmp/U+%s.tmp.png" % xcp
    subprocess.call([
        "convert", "-background", "none", "-gravity", "center",
        "-size", "16x16", "-fill", "black", "-font", font,
        "-pointsize", "16", "label:"+unichr(cp).encode("UTF-8"), tfn])
    subprocess.call(["pngcrush", "-q", "-rem", "alla", tfn, fn])
    os.unlink(tfn)
    with open(fn) as fo:
        result = b64encode(fo.read())
    os.unlink(fn)
    return result

def getUnicode():
    fontlist = getFonts()
    font_cp = {}
    for cp in xrange(0, 0x10FFFF):
        if (not (cp >= 57344 and cp <= 63743) and
            not (cp >= 983040 and cp <= 1114111)):
            font = getFontForCodepoint(fontlist, cp)
            if font:
                print '%X: %s' % (cp, font[0])
            else:
                print "####: No font for %X" % cp
            #font_cp[cp] = [font, getGlyphAsPng(font, cp)]
    return font_cp


def main():
    import pprint
    pprint.pprint(getUnicode())


if __name__ == '__main__':
    main()
    exit()

