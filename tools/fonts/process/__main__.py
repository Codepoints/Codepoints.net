#!/usr/bin/env python


import logging
import os
import os.path as op

from   process.collect import getFonts, getSVGFont, getBlocks
from   process.emit import (distributeGlyphs, generateFontFormats,
                            generateBlockSQL, generateMissingReport)
from   process.settings import TARGET_DIR


logger = logging.getLogger('codepoint.fonts')


def main():
    blocks = getBlocks()
    fonts = getFonts()
    if not op.isdir('svgsrc'):
        os.mkdir('svgsrc')
    if not op.isdir(TARGET_DIR+'fonts'):
        os.makedirs(TARGET_DIR+'fonts')
    if not op.isdir(TARGET_DIR+'sql'):
        os.makedirs(TARGET_DIR+'sql')
    if not op.isdir(TARGET_DIR+'images'):
        os.makedirs(TARGET_DIR+'images')

    for item in fonts:
        logger.info("Handling font {}".format(item[0]))
        glyphs = getSVGFont(item)
        distributeGlyphs(glyphs, blocks)

    for block, block_data in blocks.iteritems():
        logger.info("Handling block {}".format(block))
        generateFontFormats(block, block_data)
        generateBlockSQL(block, block_data)

    generateMissingReport(blocks)


if __name__ == "__main__":
    logging.basicConfig(level=logging.INFO, handler=logging.StreamHandler())
    main()

#EOF
