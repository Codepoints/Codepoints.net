""""""


import fontforge
import logging
from   lxml import etree
import subprocess
import os
import os.path as op
import gzip
from   wand.image import Image

from   process.settings import TARGET_DIR


logger = logging.getLogger('codepoint.fonts')


sql_tpl = "INSERT OR REPLACE INTO codepoint_fonts (cp, font, id, primary) VALUES (%s, '%s', '%s', %s);\n"


def distributeGlyphs(glyphs, blocks):
    """distribute glyphs to new fonts and SQL statements"""
    for glyph in glyphs:
        cp = glyph.get("unicode")
        cpn = ord(cp)
        for block, block_data in blocks.iteritems():
            if cpn in block_data["cps"]:
                block_data["cps"].remove(cpn)
                block_data["svgfontel"].append(glyph)
                block_data["sql"] += sql_tpl % (cpn, glyph.get('font-family'), block, 1)
                generateImages(glyph)
                break
            elif cpn in block_data["cps2"]:
                block_data["sql"] += sql_tpl % (cpn, glyph.get('font-family'), block, 0)
                break


def generateFontFormats(block, block_data):
    """Create several font formats from an SVG font DOM tree"""
    with open(TARGET_DIR+'fonts/'+block+'.svg', 'w') as svgfile:
        svgfile.write(etree.tostring(block_data["svgfont"]))
    font = fontforge.open(TARGET_DIR+'fonts/'+block+'.svg')
    font.generate(TARGET_DIR+'fonts/'+block+'.woff')
    font.generate(TARGET_DIR+'fonts/'+block+'.ttf')
    font.close()
    with open(TARGET_DIR+'fonts/'+block+'.eot', 'w') as eotfile:
        subprocess.call(['ttf2eot', TARGET_DIR+'fonts/'+block+'.ttf'],
                        stdout=eotfile)


def generateBlockSQL(block, block_data):
    """Generate SQL for each Unicode block about what codepoint uses which
    font"""
    with open(TARGET_DIR+'sql/'+block+'.sql', 'w') as sqlfile:
        sqlfile.write(block_data["sql"])


def generateMissingReport(blocks):
    """Collect info about which codepoints are missing from fonts"""
    report = ""
    for block, block_data in blocks.iteritems():
        if len(block_data['cps']):
            report += "{0}: {1:d}/{2:d} cps\n{3}\n".format(block,
                    len(block_data['cps']), len(block_data['cps2']), 78*"=")
            #for cp in block_data['cps']:
            #    report += 'U+{:04X} '.format(cp)
            #report += 78*"=" + "\n\n"
    with open(TARGET_DIR+'missing.txt', 'w') as _file:
        _file.write(report)


def generateImages(glyph):
    """generate one SVG and several PNG images from a glyph"""
    d = glyph.get('d')
    _unicode = ord(glyph.get('unicode'))
    if not _unicode:
        raise ValueError('No unicode: '+etree.tostring(glyph))
    if not d:
        logger.warn("no image for glyph {}".format(_unicode))
    sub = '{:02X}'.format(_unicode / 0x1000)

    if not op.isdir('{0}images/{1}'.format(TARGET_DIR, sub)):
        os.mkdir('{0}images/{1}'.format(TARGET_DIR, sub))

    svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -800 1000 1000"><path transform="scale(1,-1)" d="{}"/></svg>'.format(d)

    with gzip.open('{0}images/{1}/{2:04X}.svgz'.format(
                   TARGET_DIR, sub, _unicode), 'wb') as _file:
        _file.write(svg)

    with Image(blob=svg, format="svg") as img:
        with img.convert('png') as converted:
            converted.resize(16, 16)
            converted.save(filename='{0}images/{1}/{2:04X}.16.png'.format(
                   TARGET_DIR, sub, _unicode))
        with img.convert('png') as converted:
            converted.resize(120, 120)
            converted.save(filename='{0}images/{1}/{2:04X}.120.png'.format(
                   TARGET_DIR, sub, _unicode))


#EOF
