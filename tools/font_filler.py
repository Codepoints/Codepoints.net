#!/usr/bin/env python
#
#


from collections import OrderedDict
import fontforge
import os
import sqlite3
import subprocess
from base64 import b64encode


def get_glyph_as_png(fontfile, cp):
    """get a glyph as b64 encoded PNG file"""
    xcp = '%04X' % cp
    fn = "/tmp/U+%s.png" % xcp
    tfn = "/tmp/U+%s.tmp.png" % xcp
    subprocess.call([
        "convert", "-background", "none", "-gravity", "center",
        "-size", "16x16", "-fill", "black", "-font", fontfile,
        "-pointsize", "16", "label:"+unichr(cp).encode("UTF-8"), tfn])
    subprocess.call(["pngcrush", "-q", "-rem", "alla", tfn, fn])
    os.unlink(tfn)
    with open(fn) as fo:
        result = b64encode(fo.read())
    os.unlink(fn)
    return result


def create_font():
    """create a minimal FontForge font"""
    font = fontforge.font()
    #font.addLookup("belowbaseline","gpos_mark2base",0,
    #                [["blwm",[["deva",["dflt"]]]]])
    #font.addLookupSubtable("belowbaseline", "belowbasesub")
    #font.addAnchorClass("belowbasesub", "sub")
    return font


def copy_glyph(src, target, cp):
    """copy a glyph from one to another fontforge font"""
    src.selection.select(("unicode",), cp)
    src.copy()
    try:
        target.selection.select(("unicode",), cp)
        target.paste()
    except ValueError:
        return False
    else:
        return True


conn = sqlite3.connect('../ucd.sqlite')
c = conn.cursor()
cps = { cp: blk for cp, blk in c.execute('SELECT cp, blk '
                                         'FROM codepoints;').fetchall() }
blocks = c.execute('SELECT blk, COUNT(*) '
                   'FROM codepoints '
                   'GROUP BY blk;').fetchall()

blockfonts = { block[0]: create_font() for block in blocks }

glyph_sql = open('glyphs.sql', 'wb')


for fname in [
    "fonts/dejavu-fonts-ttf-2/dejavu-fonts-ttf-2.33/ttf/DejaVuSansMono.ttf",
    "fonts/dejavu-fonts-ttf-2/dejavu-fonts-ttf-2.33/ttf/DejaVuSerif.ttf",
    "fonts/dejavu-fonts-ttf-2/dejavu-fonts-ttf-2.33/ttf/DejaVuSans.ttf",
    "fonts/GentiumPlus-1.510/GentiumPlus-R.ttf",
    "fonts/Quivira.ttf",
    "fonts/damase_v.2.ttf",
    "fonts/Symbola.ttf",
    "fonts/Anatolian.ttf",
    "fonts/Analecta401.ttf",
    "fonts/Maya.ttf",
    "fonts/Aegyptus.ttf",
    "fonts/Musica.ttf",
    "fonts/Akkadian.ttf",
    "fonts/Aegean.ttf",
    "fonts/STIXv1/Fonts/STIX-General/STIXVariants-Regular.otf",
    "fonts/STIXv1/Fonts/STIX-General/STIXIntegralsD-Regular.otf",
    "fonts/STIXv1/Fonts/STIX-General/STIXSizeFiveSym-Regular.otf",
    "fonts/STIXv1/Fonts/STIX-General/STIXIntegralsUpD-Regular.otf",
    "fonts/STIXv1/Fonts/STIX-General/STIXIntegralsUp-Regular.otf",
    "fonts/STIXv1/Fonts/STIX-General/STIXSizeTwoSym-Regular.otf",
    "fonts/STIXv1/Fonts/STIX-General/STIXIntegralsUpSm-Regular.otf",
    "fonts/STIXv1/Fonts/STIX-General/STIXIntegralsSm-Regular.otf",
    "fonts/STIXv1/Fonts/STIX-General/STIXSizeOneSym-Regular.otf",
    "fonts/STIXv1/Fonts/STIX-General/STIXSizeThreeSym-Regular.otf",
    "fonts/STIXv1/Fonts/STIX-General/STIXGeneral-Regular.otf",
    "fonts/STIXv1/Fonts/STIX-General/STIXSizeFourSym-Regular.otf",
    "fonts/STIXv1/Fonts/STIX-Word/STIX-Regular.otf",
    "fonts/hannomH/HAN NOM B.ttf",
    "fonts/hannomH/HAN NOM A.ttf",
]:
    font = fontforge.open(fname)
    for glyph in font.glyphs():
        if glyph.unicode is -1:
            continue
        for cp, blk in cps.iteritems():
            if (glyph.unicode == cp):
                del cps[cp]
                break
            #if (glyph.unicode == cp and
            #    copy_glyph(font, blockfonts[blk], cp)):
            #    del cps[cp]
            #    if cp != 64 and cp != 92:
            #        # @ and \ have special meaning in ImageMagick
            #        glyph_sql.write("INSERT INTO codepoint_image (cp, image) "
            #            "VALUES ({}, '{}');\n".format(cp,
            #                                    get_glyph_as_png(fname, cp)))
            #    break
    font.close()


glyph_sql.close()


#for block, font in blockfonts.iteritems():
#    print('{} of {} ({})'.format(len([x for x in font.glyphs()]),
#                                 dict(blocks)[block], block))
#    font.fontname = block
#    font.save('../codepoints.net/static/fonts/blocks/{}.woff'.format(block))

print "Missing glyphs:\n", '\n'.join(map(lambda s: 'U+{0:X}'.format(s), cps.keys()))
print "len(missing):\n", len(cps)


#EOF
