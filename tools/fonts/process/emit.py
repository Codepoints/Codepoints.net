"""Emit various representations of a font glyph"""


import fontforge
import gzip
import json
import logging
from   lxml import etree
import os
import os.path as op
import subprocess
from   time import sleep

from   process.collect import get_all_codepoints
from   process.clean import clean_images, clean_sql, clean_font
from   process.settings import TARGET_DIR, EM_SIZE, PNG_WIDTHS, DB_PNG_WIDTH, \
                               CREATE_IMAGES


logger = logging.getLogger('codepoint.fonts')


xmlns_svg = 'http://www.w3.org/2000/svg'

svg_font_skeleton = '''<svg xmlns="'''+xmlns_svg+'''" version="1.1">
  <defs>
    <font id="{{0}}" horiz-adv-x="{0}">
      <font-face
        font-family="{{0}}"
        font-weight="400"
        units-per-em="{0}"/>
      {{1}}
    </font>
  </defs>
</svg>'''.format(EM_SIZE)


def generate_missing_report(cps):
    """Collect info about which codepoints are missing from fonts"""
    all_cps = get_all_codepoints()
    blocks = {}
    for cp, block in all_cps:
        blocks[block] = blocks.get(block, [0, 0, []])
        blocks[block][0] += 1
        if cp in cps:
            blocks[block][1] += 1
        else:
            blocks[block][2].append(cp)

    with open(TARGET_DIR+'report.txt', 'w') as _file:
        for block, info in blocks.iteritems():
            missing = ''
            marker = 'O'
            if info[0] > info[1]:
                marker = 'X'
            if info[1] and info[0] > info[1]:
                missing = '\n    Missing: {}'.format(
                        ' '.join([
                            'U+{:04X}'.format(cp)
                            for cp in info[2] ]))
            _file.write("""{} {: 3.0F}% of block {} ({} of {}){}

""".format(marker, info[1]*100/info[0], block, info[1], info[0], missing))

    with open(TARGET_DIR+'cache/cache.json', 'w') as _file:
        # store as .items() to preserve numeric key
        json.dump(cps.items(), _file)


def emit_png(cp):
    """Create a PNG from a previously generated SVG. Expects
    the appropriate .svgz file in place. Also append PNG data to SQL file
    for populating codepoint_image table."""
    ocp = ord(cp)
    processes = []

    for width in PNG_WIDTHS:
        target = '{0}images/{1:02X}/{2:04X}.{3}.png'.format(TARGET_DIR,
                        ocp / 0x1000, ocp, width)
        args = [
            'inkscape',
                '--without-gui', '--export-background-opacity=0',
                '--export-png={}'.format(target),
                '--export-width={}'.format(width),
                '{0}images/{1:02X}/{2:04X}.svgz'.format(
                    TARGET_DIR, ocp / 0x1000, ocp),
                '1>&2',
            '&&',
            'optipng',
                '-quiet',
                '-o7',
                target,
        ]
        if width == DB_PNG_WIDTH:
            # we use w=16 icons as source for data URIs and store them
            # in the DB
            args.extend([
                '&&',
                'touch',
                    '{0}sql/{1:02X}_img.sql'.format(TARGET_DIR, ocp / 0x1000),
                '&&',
                'base64',
                    '-w0',
                    target,
                    '|',
                'cat',
                    '<(echo -n "INSERT OR REPLACE INTO codepoint_image (cp, image) VALUES ({}, \'")'.format(ocp),
                    '-',
                    '<(echo "\');")',
                    '>> {0}sql/{1:02X}_img.sql'.format(TARGET_DIR, ocp / 0x1000),
            ])
        logger.debug(' '.join(args))
        with open(os.devnull, 'w') as devnull:
            processes.append(subprocess.Popen(' '.join(args), shell=True, executable="/bin/bash",
                stderr=devnull))
    return processes


def emit_svg(cp, d):
    """Create an SVG(Z) file from glyph data d.

    Will auto-correct the width to center the glyph."""
    ocp = ord(cp)
    svg = ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 {2} {1} {1}">'
             '<path id="glyph" transform="scale(1,-1)" d="{0}"/>'
           '</svg>').format(d, EM_SIZE, EM_SIZE*-0.8)
    p = subprocess.Popen(["inkscape", "--without-gui", "--query-id=glyph",
        "--query-width", "/proc/self/fd/0"], stdin=subprocess.PIPE,
        stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    width = float(p.communicate(svg)[0])
    if width > EM_SIZE:
        logger.warning('glyph too wide: {0}, {1:04X}: {2}'
                       .format(cp.encode('utf-8'), ocp, width))
    svg = (svg.replace(' id="glyph"', '')
              .replace(' viewBox="0',
                       ' viewBox="{}'.format(-(EM_SIZE - width)/2)))

    with gzip.open('{0}images/{1:02X}/{2:04X}.svgz'.format(
                   TARGET_DIR, ocp / 0x1000, ocp), 'wb') as _file:
        _file.write(svg)
    return svg


def emit_images(cp, d):
    """Take a codepoint and its glyph path data and create all needed
    images"""
    if not CREATE_IMAGES:
        return []

    sub = '{:02X}'.format(ord(cp) / 0x1000)

    if not op.isdir('{0}images/{1}'.format(TARGET_DIR, sub)):
        os.mkdir('{0}images/{1}'.format(TARGET_DIR, sub))

    emit_svg(cp, d)
    processes = emit_png(cp)
    return processes


def emit_sql(cp, font_family, primary=1):
    """Create a SQL row with font info for this CP.
    If primary=1, the font is the one used to render the example glyph."""

    sql_tpl = ('INSERT OR REPLACE INTO codepoint_fonts (cp, font, "id", "primary") '
               "VALUES (%s, '%s', '%s', %s);\n")

    sql = sql_tpl % (ord(cp), font_family,
            font_family, # TODO: make this a useful entry!
            primary)

    with open('{0}sql/{1:02X}.sql'.format(TARGET_DIR,
            ord(cp) / 0x1000), 'a') as sqlfile:
        sqlfile.write(sql)


def emit_font(cp, d, block):
    """Write the glyph element to a cache file for later pick-up
    by another method"""
    with open('{}cache/font_{}.tmp'.format(TARGET_DIR, block[0]), 'a') as font:
        font.write('<glyph unicode="&#%s;" d="%s"/>\n' % (ord(cp), d))


def emit(cp, d, font_family, block):
    """take the path and do whatever magic is needed"""
    processes = []
    try:
        done = 0
        processes = emit_images(cp, d)
        done = 1
        emit_font(cp, d, block)
        done = 2
        emit_sql(cp, font_family)
        done = 3
    except (KeyboardInterrupt, SystemExit):
        i = 0
        while len(filter(lambda p: p.poll() is None, processes)):
            if i % 6 == 0:
                logger.warning('Waiting for subprocesses to end...')
            i += 1
            sleep(0.5)
        if done < 3:
            # clean up
            logger.warning('Clean up last cp: U+{:04X}'.format(ord(cp)))
            # TODO: do the clean-up!
            clean_images(cp)
            if done > 2:
                clean_sql(cp, font_family)
            if done > 1:
                clean_font(cp, block)
        else:
            logger.info('No clean-up needed. Last cp: U+{:04X}'.format(ord(cp)))
        raise
    return True


def finish_fonts(blocks):
    """Create several font formats from an SVG font DOM tree"""
    for blk in blocks:
        block = blk[0]
        cache = '{}cache/font_{}.tmp'.format(TARGET_DIR, block)
        if not op.isfile(cache) or \
           not os.stat(cache).st_size:
            logger.debug('No font created for block {}...'.format(block))
            continue
        else:
            logger.info('Creating font for block {}...'.format(block))
        with open(TARGET_DIR+'fonts/'+block+'.svg', 'w') as svgfile:
            with open(cache) as _cache:
                svgfile.write(svg_font_skeleton.format(block, _cache.read()))
        font = fontforge.open(TARGET_DIR+'fonts/'+block+'.svg')
        font.generate(TARGET_DIR+'fonts/'+block+'.woff')
        font.generate(TARGET_DIR+'fonts/'+block+'.ttf')
        font.close()
        with open(TARGET_DIR+'fonts/'+block+'.eot', 'w') as eotfile:
            subprocess.call(['ttf2eot', TARGET_DIR+'fonts/'+block+'.ttf'],
                            stdout=eotfile)


#EOF
