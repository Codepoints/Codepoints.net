""""""

import fontforge
import json
import logging
from   lxml import etree
import os.path as op
import sqlite3
import subprocess
from   process.settings import EM_SIZE


logger = logging.getLogger('codepoint.fonts')


ffGlyphXPath = etree.XPath('//glyph[string-length(@unicode) = 1]')
xmlns_svg = 'http://www.w3.org/2000/svg'
fontXPath = etree.XPath('//svg:font', namespaces={ 'svg': xmlns_svg })


svg_font_skeleton = '''<svg xmlns="'''+xmlns_svg+'''" version="1.1">
  <defs>
    <font id="%s" horiz-adv-x="{0}">
      <font-face
        font-family="%s"
        font-weight="400"
        units-per-em="{0}"/>
    </font>
  </defs>
</svg>'''.format(EM_SIZE)


def getFonts():
    """get a prioretized list of fonts to respect"""
    fonts = json.load(open('fonts.json'))
    payload = []
    for font in fonts:
        if font[0][0] == "#":
            logger.info('skip {}'.format(font[0][1:]))
            continue
        font.insert(0, op.basename(op.splitext(font[0])[0]))
        payload.append(font)

    return payload


def getSVGFont(item):
    """convert a TTF to SVG and return the eTree <glyph>s"""
    svgfont = 'svgsrc/'+item[0]+'.svg'
    if not op.isfile(svgfont):
        font = fontforge.open(item[1])
        font.em = EM_SIZE
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
        cps = [ x[0] for x in
            cur.execute('SELECT cp FROM codepoints WHERE blk = ?', (block, ))
               .fetchall()
        ]
        prefix = ""
        if len(cps) > 2500:
            prefix = "1"
        for x in range(1, len(cps)/2500 + 2):
            name = block + prefix
            prefix = str(x+1)
            if name not in blocks:
                blocks[name] = {}
            blocks[name]["cps"] = cps[(x-1)*2500:x*2500]
            blocks[name]["cps2"] = cps[(x-1)*2500:x*2500]
            blocks[name]["svgfont"] = etree.XML(svg_font_skeleton % (block, block))
            blocks[name]["svgfontel"] = fontXPath(blocks[name]["svgfont"])[0]
            blocks[name]["sql"] = ""

    return {block: block_data for block, block_data in blocks.iteritems() if "cps" in block_data}


