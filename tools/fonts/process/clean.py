"""Clean up various stuff, e.g. after SystemExit events"""


import errno
import fileinput
import logging
import os
import re

from   process.settings import TARGET_DIR, PNG_WIDTHS, DB_PNG_WIDTH


logger = logging.getLogger('codepoint.fonts')


def clean_png(cp):
    """"""
    ocp = ord(cp)

    for width in PNG_WIDTHS:
        try:
            os.remove('{0}images/{1:02X}/{2:04X}.{3}.png'.format(TARGET_DIR,
                        ocp / 0x1000, ocp, width))
        except OSError as e:
            if e.errno != errno.ENOENT:
                raise
        if width == DB_PNG_WIDTH:
            _strip_line_from_file(
                '{0}sql/{1:02X}_img.sql'.format(TARGET_DIR, ocp / 0x1000),
                r'INSERT OR REPLACE INTO codepoint_image \(cp, image\) VALUES \({}, \''.format(ocp))


def clean_svg(cp):
    """"""
    ocp = ord(cp)
    try:
        os.remove('{0}images/{1:02X}/{2:04X}.svgz'.format(TARGET_DIR, ocp / 0x1000, ocp))
    except OSError as e:
        if e.errno != errno.ENOENT:
            raise


def clean_images(cp):
    """"""
    sub = '{:02X}'.format(ord(cp) / 0x1000)

    clean_svg(cp)
    clean_png(cp)

    try:
        os.rmdir('{0}images/{1}'.format(TARGET_DIR, sub))
    except OSError as e:
        if e.errno != errno.ENOTEMPTY:
            raise


def clean_sql(cp, font_family, primary=1):
    """"""
    ocp = ord(cp)

    _strip_line_from_file(
        '{0}sql/{1:02X}.sql'.format(TARGET_DIR, ocp / 0x1000),
        r"INSERT OR REPLACE INTO codepoint_fonts \(cp, font, id, primary\) VALUES ({}, '{}',".format(ocp, font_family))


def clean_font(cp, blocks):
    """"""
    ocp = ord(cp)

    for blk in blocks:
        if blk[1] <= ocp and blk[2] >= ocp:
            _strip_line_from_file(
                '{}cache/font_{}.tmp'.format(TARGET_DIR, blk[0]),
                r'<glyph unicode="&#{};" d="'.format(ocp))
            return True


def _strip_line_from_file(file_, regex):
    """Strip a line matching regex from a file

    see http://stackoverflow.com/a/17221420/113195
    """
    if os.path.isfile(file_):
        for line in fileinput.input(file_, inplace=True):
            if not re.search(regex, line):
                print line,


#EOF
