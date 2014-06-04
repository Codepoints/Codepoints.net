#!/usr/bin/env python


import json
import logging
from   lxml import etree
import os
import os.path as op

from   process.collect import get_blocks, load_cache
from   process.emit import emit, emit_sql, finish_fonts, generate_missing_report
from   process.settings import TARGET_DIR, LOG_LEVEL


logger = logging.getLogger('codepoint.fonts')


def init():
    """"""
    if not op.isdir('svgsrc'):
        os.mkdir('svgsrc')
    for dir_ in ['fonts', 'sql', 'images']:
        if not op.isdir(TARGET_DIR+dir_):
            os.makedirs(TARGET_DIR+dir_)
    _handler = logging.StreamHandler()
    _handler.setFormatter(logging.Formatter(
        "[fontprocessor:%(levelname)s] %(message)s"))
    logger.addHandler(_handler)
    _handler = logging.FileHandler(TARGET_DIR+'process.log')
    _handler.setFormatter(logging.Formatter(
        "[%(levelname)s] %(message)s"))
    logger.addHandler(_handler)
    logger.setLevel(LOG_LEVEL)


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
        logger.info("Handling font {} ({}/{})".format(font_file, item, len_fontlist))
        with open(font_file) as svg:
            r = etree.parse(svg)
        glyphs = ffGlyphXPath(r)
        len_glyphs = len(glyphs)
        for i, glyph in enumerate(glyphs):
            # TODO: DEBUG
            if counter > 3: break
            cp = glyph.get("unicode")
            ocp = ord(cp)
            logger.info("  | Glyph {:3d}/{} ({}) of {}".format(i+1, len_glyphs,
                        cp.encode('utf-8') if ocp >= 32 else '?', font_file))
            cpn = '{0:04X}'.format(ocp)
            d = glyph.get("d", False)
            if d and ocp not in cps:
                counter += 1
                cps[ocp] = [ font_family ]
                emit(cp, d, font_family, blocks)
            elif ocp in cps:
                emit_sql(cp, font_family, 0)
                cps[ocp].append(font_family)

    finish_fonts(blocks)

    #with open('result.json', 'wb') as result:
    #    json.dump(cps, result, sort_keys=True,
    #                           indent=4, separators=(',', ': '))

    logger.info("Handled {} cps".format(counter))

    generate_missing_report(cps)


if __name__ == "__main__":
    init()
    main()


#EOF
