#!/usr/bin/env python


import json
import logging
from   lxml import etree
import os
import os.path as op
import signal
import sys
sys.path.append(op.dirname(op.dirname(__file__)))

from   process.collect import get_blocks, load_cache
from   process.emit import emit, emit_sql, finish_fonts, generate_missing_report
from   process.settings import TARGET_DIR, LOG_LEVEL


logger = logging.getLogger('codepoint.fonts')


def init():
    """"""
    def raiser(signal, frame):
        logger.warning('Received signal for shutdown. Cleaning up...')
        raise KeyboardInterrupt
        sys.exit(0)
    signal.signal(signal.SIGINT, raiser)
    if not op.isdir('svgsrc'):
        os.mkdir('svgsrc')
    for dir_ in ['fonts', 'sql', 'images', 'cache']:
        if not op.isdir(TARGET_DIR+dir_):
            os.makedirs(TARGET_DIR+dir_)
    _handler = logging.StreamHandler()
    _handler.setFormatter(logging.Formatter(
        "[%(levelname)s] %(message)s"))
    _handler.setLevel(LOG_LEVEL)
    logger.addHandler(_handler)
    _handler = logging.FileHandler(TARGET_DIR+'process.log')
    _handler.setFormatter(logging.Formatter(
        "[%(asctime)s %(levelname)s] %(message)s"))
    _handler.setLevel(logging.DEBUG)
    logger.addHandler(_handler)
    logger.setLevel(logging.DEBUG)


def main():
    """"""
    with open('font-family.json') as _fontlist:
        fontlist = json.load(_fontlist)

    ffGlyphXPath = etree.XPath('//glyph[string-length(@unicode) = 1]')

    blocks = get_blocks()

    len_fontlist = len(fontlist)
    cps = load_cache()
    counter = 0

    for item, (font_file, font_family) in enumerate(fontlist):
        if font_file[0] == "#":
            logger.warning("Skipping font {}".format(font_file[1:]))
            continue

        logger.info("Handling font {} ({}/{})".format(font_file, item, len_fontlist))

        try:
            with open(font_file) as svg:
                r = etree.parse(svg)
        except IOError:
            logger.warning('Could not open %s' % font_file)
            continue
        except etree.XMLSyntaxError:
            logger.warning('Could not parse %s, no XML' % font_file)
            continue
        glyphs = ffGlyphXPath(r)
        len_glyphs = len(glyphs)

        for i, glyph in enumerate(glyphs):
            cp = glyph.get("unicode")
            ocp = ord(cp)
            cpn = '{0:04X}'.format(ocp)
            d = glyph.get("d", False)
            done = False
            if 0xE000 <= ocp <= 0xF8FF or \
               0xF0000 <= ocp <= 0x10FFFF:
                # private use areas. Skip.
                continue

            for blk in blocks:
                if blk[1] <= ocp and blk[2] >= ocp:

                    try:
                        if d and ocp not in cps:
                            counter += 1
                            logger.info("  | Glyph {:3d}/{} ({}) of {}".format(i+1, len_glyphs,
                                cp.encode('utf-8') if ocp >= 32 else '?', font_file))
                            emit(cp, d, font_family, blk)
                            cps[ocp] = [ font_family ]
                        elif ocp in cps and font_family not in cps[ocp]:
                            logger.debug("  | Glyph {:3d}/{} ({}) of {}".format(i+1, len_glyphs,
                                cp.encode('utf-8') if ocp >= 32 else '?', font_file))
                            emit_sql(cp, font_family, 0)
                            cps[ocp].append(font_family)
                    except (KeyboardInterrupt, SystemExit):
                        if cps and ocp and ocp in cps:
                            del cps[ocp]
                        logger.warning('Shutting down, creating fonts and reports.')
                        finish(cps, blocks, counter)
                        raise

                    done = True
                    break

            if not done:
                logger.warning('No block found for U+{:04X}: not processed.'.format(ocp))

    finish(cps, blocks, counter)


def finish(cps, blocks, counter):
    """end the processing with the appropriate steps"""
    logger.info("Handled {} cps".format(counter))
    try:
        finish_fonts(blocks)
    except (KeyboardInterrupt, SystemExit):
        pass
    generate_missing_report(cps)


if __name__ == "__main__":
    init()
    try:
        main()
    except (KeyboardInterrupt, SystemExit):
        print "System shutting down"


#EOF
