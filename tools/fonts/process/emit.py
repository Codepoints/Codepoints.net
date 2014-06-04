"""Emit various representations of a font glyph"""


import fontforge
import json
import logging
from   lxml import etree
import subprocess
import os
import os.path as op
import gzip

from   process.collect import get_all_codepoints
from   process.settings import TARGET_DIR, EM_SIZE, PNG_WIDTHS, DB_PNG_WIDTH


logger = logging.getLogger('codepoint.fonts')


sql_tpl = ("INSERT OR REPLACE INTO codepoint_fonts (cp, font, id, primary) "
           "VALUES (%s, '%s', '%s', %s);\n")
sqlimg_tpl = ("INSERT OR REPLACE INTO codepoint_image (cp, image) "
              "VALUES (%s, '%s');\n")

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
            if info[1]:
                missing = '\n    Missing: {}'.format(
                        ' '.join([
                            'U+{:04X}'.format(cp)
                            for cp in info[2] ]))
            _file.write("""Block {}:
    {} of {} ({: 3.0F}%){}

""".format(block, info[1], info[0], info[1]*100/info[0], missing))

    with open(TARGET_DIR+'cache.json', 'w') as _file:
        # store as .items() to preserve numeric key
        json.dump(cps.items(), _file)


def emit_png(cp):
    """Create a PNG from a previously generated SVG. Expects
    the appropriate .svgz file in place. Also append PNG data to SQL file
    for populating codepoint_image table."""
    ocp = ord(cp)
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
            subprocess.Popen(' '.join(args), shell=True, executable="/bin/bash",
                stderr=devnull)
    return True


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
    sub = '{:02X}'.format(ord(cp) / 0x1000)

    if not op.isdir('{0}images/{1}'.format(TARGET_DIR, sub)):
        os.mkdir('{0}images/{1}'.format(TARGET_DIR, sub))

    emit_svg(cp, d)
    emit_png(cp)
    return True


def emit_sql(cp, font_family, primary=1):
    """Create a SQL row with font info for this CP.
    If primary=1, the font is the one used to render the example glyph."""

    sql = sql_tpl % (ord(cp), font_family,
            font_family, # TODO: make this a useful entry!
            primary)

    with open('{0}sql/{1:02X}.sql'.format(TARGET_DIR,
            ord(cp) / 0x1000), 'a') as sqlfile:
        sqlfile.write(sql)


def emit_font(cp, d, blocks):
    """"""
    ocp = ord(cp)

    for blk in blocks:
        if blk[1] <= ocp and blk[2] >= ocp:
            if len(blk) < 4:
                blk.append(etree.XML(svg_font_skeleton % (blk[0], blk[0])))
                blk.append(fontXPath(blk[3]))
            blk[4].append(etree.XML('<glyph unicode="&#%s;" d="%s"/>' % (ocp, d)))
            return True

    logger.warning('No block found for U+{:04X}'.format(ocp))
    return False


def emit(cp, d, font_family, blocks):
    """take the path and do whatever magic is needed"""
    emit_images(cp, d)
    emit_font(cp, d, blocks)
    emit_sql(cp, font_family)
    return True


def finish_fonts(blocks):
    """Create several font formats from an SVG font DOM tree"""
    for blk in blocks:
        block = blk[0]
        if len(blk) < 4:
            logger.warning('No fonts created for block {}!'.format(block))
            continue
        with open(TARGET_DIR+'fonts/'+block+'.svg', 'w') as svgfile:
            svgfile.write(etree.tostring(blk[3]))
        font = fontforge.open(TARGET_DIR+'fonts/'+block+'.svg')
        font.generate(TARGET_DIR+'fonts/'+block+'.woff')
        font.generate(TARGET_DIR+'fonts/'+block+'.ttf')
        font.close()
        with open(TARGET_DIR+'fonts/'+block+'.eot', 'w') as eotfile:
            subprocess.call(['ttf2eot', TARGET_DIR+'fonts/'+block+'.ttf'],
                            stdout=eotfile)


#EOF
