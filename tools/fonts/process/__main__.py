#!/usr/bin/env python


from   collections import OrderedDict
import json
import logging
from   lxml import etree
import os
import os.path as op

from   process.collect import getBlocks
from   process.emit import emit, emit_sql
from   process.settings import TARGET_DIR


logger = logging.getLogger('codepoint.fonts')


ffGlyphXPath = etree.XPath('//glyph[string-length(@unicode) = 1]')


def init():
    """"""
    if not op.isdir('svgsrc'):
        os.mkdir('svgsrc')
    for dir_ in ['fonts', 'sql', 'images']:
        if not op.isdir(TARGET_DIR+dir_):
            os.makedirs(TARGET_DIR+dir_)


def main():
    """"""
    init()
    cps = {}
    counter = 0
    with open('font-family.json') as _fontlist:
        fontlist = json.load(_fontlist)

    for font_file, font_family in fontlist:
        logger.info("Handling font {}".format(font_file))
        with open(font_file) as svg:
            r = etree.parse(svg)
        for glyph in ffGlyphXPath(r):
            cp = glyph.get("unicode")
            cpn = '{0:04X}'.format(ord(cp))
            d = glyph.get("d", False)
            if d and cpn not in cps:
                counter += 1
                cps[cpn] = [ d, font_family ]
                emit(cp, d, font_family)
            elif cpn in cps:
                emit_sql(cp, font_family, 0)
                cps[cpn].append(font_family)

    with open('result.json', 'wb') as result:
        json.dump(cps, result, sort_keys=True,
                               indent=4, separators=(',', ': '))

    logger.info("Handled {} cps".format(counter))

    #blocks, fonts = init()

    #for item in fonts:
    #    logger.info("Handling font {}".format(item[0]))
    #    glyphs = getSVGFont(item)
    #    distributeGlyphs(glyphs, blocks)

    #for block, block_data in blocks.iteritems():
    #    logger.info("Handling block {}".format(block))
    #    generateFontFormats(block, block_data)
    #    generateBlockSQL(block, block_data)

    #generateMissingReport(blocks)


if __name__ == "__main__":
    _handler = logging.StreamHandler()
    _handler.setFormatter(logging.Formatter(
        "[fontprocessor:%(levelname)s] %(message)s"))
    logger.setLevel(logging.INFO)
    logger.setLevel(logging.DEBUG)
    logger.addHandler(_handler)
    main()


#EOF
