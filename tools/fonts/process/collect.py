""""""

import fontforge
import json
import logging
from   lxml import etree
import os.path as op
import sqlite3
import subprocess


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


def getFonts():
    """get a prioretized list of fonts to respect"""
    fonts = json.load(open('fonts.json'))
    return [(op.basename(op.splitext(font)[0]), font) for font in fonts if font[0] != "#"]


def getSVGFont(item):
    """convert a TTF to SVG and return the eTree <glyph>s"""
    svgfont = 'svgsrc/'+item[0]+'.svg'
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
    conn = sqlite3.connect('../../ucd.sqlite')
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


