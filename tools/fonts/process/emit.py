""""""


import fontforge
import logging
from   lxml import etree
import subprocess

from   process.settings import TARGET_DIR


logger = logging.getLogger('codepoint.fonts')


sql_tpl = "INSERT OR REPLACE INTO codepoint_fonts (cp, font, id) VALUES (%s, '%s', '%s');"


def distributeGlyphs(glyphs, blocks):
    """distribute glyphs to new fonts and SQL statements"""
    for glyph in glyphs:
        cp = glyph.get("unicode")
        cpn = ord(cp)
        for block, block_data in blocks.iteritems():
            if cpn in block_data["cps"]:
                block_data["cps"].remove(cpn)
                block_data["svgfontel"].append(glyph)
                block_data["sql"] += sql_tpl % (cpn, glyph.get('font-family'), block)
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
                    len(block_data['cps']), block_data['len_cps'], 78*"=")
            #for cp in block_data['cps']:
            #    report += 'U+{:04X} '.format(cp)
            #report += 78*"=" + "\n\n"
    with open(TARGET_DIR+'missing.txt', 'w') as _file:
        _file.write(report)


#EOF
