""""""


import fontforge
import logging
from   lxml import etree
import subprocess


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
    """"""
    with open('blockfonts/'+block+'.svg', 'w') as svgfile:
        svgfile.write(etree.tostring(block_data["svgfont"]))
    font = fontforge.open('blockfonts/'+block+'.svg')
    font.generate('blockfonts/'+block+'.woff')
    font.generate('blockfonts/'+block+'.ttf')
    font.close()
    subprocess.call([
        'ttf2eot',
            'blockfonts/'+block+'.ttf',
            'blockfonts/'+block+'.eot',
    ])


def generateBlockSQL(block, block_data):
    """"""
    with open('blockfont_sql/'+block+'.sql', 'w') as sqlfile:
        sqlfile.write(block_data["sql"])


#EOF
