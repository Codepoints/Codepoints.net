#!/usr/bin/env python


import fontforge
import json
import logging
from   lxml import etree
import os
import os.path as op
import sqlite3
import subprocess
import sys


logger = logging.getLogger('codepoint.fonts')


ffGlyphXPath = etree.XPath('//glyph[string-length(@unicode) = 1]')
xmlns_svg = 'http://www.w.3.org/2000/svg'
glyphXPath = etree.XPath('//svg:glyph', namespaces={ 'svg': xmlns_svg })
fontXPath = etree.XPath('//svg:font', namespaces={ 'svg': xmlns_svg })


svg_font_skeleton = '''<svg xmlns="'''+xmlns_svg+'''" version="1.1">
  <defs>
    <font id="%s" horiz-adv-x="1000">
      <font-face
        font-family="%s"
        font-weight="400"
        units-per-em="1000"/>
    </font>
  </defs>
</svg>'''


sql_tpl = "INSERT OR REPLACE INTO codepoint_fonts (cp, font, id) VALUES (%s, '%s', '%s');"


def getFonts():
    """get a prioretized list of fonts to respect"""
    fonts = json.load(open('fonts.json'))
    return [(op.basename(op.splitext(font)[0]), font) for font in fonts]


def createSVGFont(item):
    """convert a TTF to SVG and return the eTree <glyph>s"""
    svgfont = 'svgfonts/'+item[0]+'.svg'
    if not op.isfile(svgfont):
        font = fontforge.open(item[1])
        font.em = 1000
        font.generate(svgfont)
        font.close()
        subprocess.call([
            'sed',
                '-i',
                's/<glyph\\b/& font-family="'+item[0]+'"/g',
                svgfont
        ])
    with open(svgfont) as svg:
        r = etree.parse(svg)
    return ffGlyphXPath(r)


def getBlocks():
    """get all blocks and their codepoints"""
    conn = sqlite3.connect('../ucd.sqlite')
    cur = conn.cursor()

    # init blocks as dict keys
    blocks = {
        x[0]: {}
        for x in
            cur.execute('SELECT DISTINCT blk FROM codepoints;').fetchall() }

    for block in blocks.keys():
        blocks[block]["cps"] = [ x[0] for x in
            cur.execute('SELECT cp FROM codepoints WHERE blk = ?', (block, ))
               .fetchall()
        ]
        blocks[block]["svgfont"] = etree.XML(svg_font_skeleton % (block, block))
        blocks[block]["svgfontel"] = fontXPath(blocks[block]["svgfont"])[0]
        blocks[block]["sql"] = ""

    return blocks


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


def main():
    blocks = getBlocks()
    fonts = getFonts()
    for item in fonts:
        logger.info("Handling font {}".format(item[0]))
        glyphs = createSVGFont(item)
        distributeGlyphs(glyphs, blocks)
    for block, block_data in blocks.iteritems():
        logger.info("Handling block {}".format(block))
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
        with open('blockfont_sql/'+block+'.sql', 'w') as sqlfile:
            sqlfile.write(block_data["sql"])


if __name__ == "__main__":
    logging.basicConfig(level=logging.INFO, handler=logging.StreamHandler())
    main()

#EOF
