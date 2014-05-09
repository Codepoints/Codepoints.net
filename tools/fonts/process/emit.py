""""""


from   base64 import b64encode
import fontforge
import logging
from   lxml import etree
import subprocess
import os
import os.path as op
import gzip
from   wand.color import Color
from   wand.image import Image

from   process.settings import TARGET_DIR, EM_SIZE


logger = logging.getLogger('codepoint.fonts')


sql_tpl = ("INSERT OR REPLACE INTO codepoint_fonts (cp, font, id, primary) "
           "VALUES (%s, '%s', '%s', %s);\n")
sqlimg_tpl = ("INSERT OR REPLACE INTO codepoint_image (cp, image) "
              "VALUES (%s, '%s');\n")


transparent = Color('transparent')


def distributeGlyphs(glyphs, blocks):
    """distribute glyphs to new fonts and SQL statements"""
    for glyph in glyphs:
        cp = glyph.get("unicode")
        cpn = ord(cp)
        for block, block_data in blocks.iteritems():
            if cpn in block_data["cps"]:
                block_data["cps"].remove(cpn)
                block_data["svgfontel"].append(glyph)
                block_data["sql"] += sql_tpl % (cpn, glyph.get('font-family'),
                                                block, 1)
                generateImages(glyph, block_data)
                break
            elif cpn in block_data["cps2"]:
                block_data["sql"] += sql_tpl % (cpn, glyph.get('font-family'),
                                                block, 0)
                break


def generateFontFormats(block, block_data):
    """Create several font formats from an SVG font DOM tree"""
    with open(TARGET_DIR+'fonts/'+block+'.svg', 'w') as svgfile:
        svgfile.write(etree.tostring(block_data["svgfont"]))
    font = fontforge.open(TARGET_DIR+'fonts/'+block+'.svg')
    font.generate(TARGET_DIR+'fonts/'+block+'.woff')
    font.generate(TARGET_DIR+'fonts/'+block+'.ttf')
    font.close()
    with open(TARGET_DIR+'fonts/'+block+'.eot', 'w') as eotfile:
        subprocess.call(['ttf2eot', TARGET_DIR+'fonts/'+block+'.ttf'],
                        stdout=eotfile)


def generateBlockSQL(block, block_data):
    """Generate SQL for each Unicode block about what codepoint uses which
    font"""
    with open(TARGET_DIR+'sql/'+block+'.sql', 'w') as sqlfile:
        sqlfile.write(block_data["sql"])


def generateMissingReport(blocks):
    """Collect info about which codepoints are missing from fonts"""
    report = ""
    for block, block_data in blocks.iteritems():
        if len(block_data['cps']):
            report += "{0}: {1:d}/{2:d} cps\n{3}\n".format(block,
                    len(block_data['cps']), len(block_data['cps2']), 78*"=")
            #for cp in block_data['cps']:
            #    report += 'U+{:04X} '.format(cp)
            #report += 78*"=" + "\n\n"
    with open(TARGET_DIR+'missing.txt', 'w') as _file:
        _file.write(report)


def generateImages(glyph, block_data):
    """generate one SVG and several PNG images from a glyph"""
    d = glyph.get('d')
    _unicode = ord(glyph.get('unicode'))
    if not _unicode:
        raise ValueError('No unicode: '+etree.tostring(glyph))
    if not d:
        logger.warn("no image for glyph {}".format(_unicode))
        return False
    sub = '{:02X}'.format(_unicode / 0x1000)

    if not op.isdir('{0}images/{1}'.format(TARGET_DIR, sub)):
        os.mkdir('{0}images/{1}'.format(TARGET_DIR, sub))

    svg = ('<svg xmlns="http://www.w3.org/2000/svg"'
               ' viewBox="0 {2} {1} {1}">'
             '<path id="glyph" transform="scale(1,-1)" d="{0}"/>'
           '</svg>').format(d, EM_SIZE, EM_SIZE*-0.8)
    p = subprocess.Popen(["inkscape", "--without-gui", "--query-id=glyph",
        "--query-width", "/proc/self/fd/0"],
        stdin=subprocess.PIPE, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    width = float(p.communicate(svg)[0])
    if width > EM_SIZE:
        logger.info('glyph too wide: {}, {}: {}'.format(
            _unicode, unichr(_unicode).encode('utf-8'), width))
    svg = (svg.replace(' id="glyph"', '')
              .replace(' viewBox="0', ' viewBox="{}'
                       .format(-(EM_SIZE - width)/2)))

    with gzip.open('{0}images/{1}/{2:04X}.svgz'.format(
                   TARGET_DIR, sub, _unicode), 'wb') as _file:
        _file.write(svg)

    with Image(blob=svg, format="svg") as img:
        img.background_color = transparent
        with img.convert('png') as converted:
            converted.resize(16, 16)
            converted.save(filename='{0}images/{1}/{2:04X}.16.png'.format(
                   TARGET_DIR, sub, _unicode))
            block_data['sql'] += sqlimg_tpl % (_unicode,
                b64encode(converted.make_blob()))
        with img.convert('png') as converted:
            converted.resize(120, 120)
            converted.save(filename='{0}images/{1}/{2:04X}.120.png'.format(
                   TARGET_DIR, sub, _unicode))

    return True


def emit_png(cp):
    """Create a PNG from a previously generated SVG. Expects
    the appropriate .svgz file in place."""
    ocp = ord(cp)
    for width in [16, 120]:
        target = '{0}images/{1:02X}/{2:04X}.{3}.png'.format(TARGET_DIR,
                        ocp / 0x1000, ocp, width)
        args = [
            'inkscape',
                '--without-gui', '--export-background-opacity=0',
                '--export-png={}'.format(target),
                '--export-width={}'.format(width),
                '{0}images/{1:02X}/{2:04X}.svgz'.format(
                    TARGET_DIR, ocp / 0x1000, ocp),
            '&&',
            'optipng',
                '-quiet',
                '-o7',
                target,
        ]
        if width == 16:
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
                    '<(echo -n "INSERT OR IGNORE INTO codepoint_image (cp, image) VALUES ({}, \'")'.format(ocp),
                    '-',
                    '<(echo "\');")',
                    '>> {0}sql/{1:02X}_img.sql'.format(TARGET_DIR, ocp / 0x1000),
            ])
        logger.debug(' '.join(args))
        with open(os.devnull, 'w') as devnull:
            subprocess.Popen(' '.join(args), shell=True, executable="/bin/bash"
                    #stdout=devnull, stderr=devnull)
                    )
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
        logger.info('glyph too wide: {0}, {1:04X}: {2}'
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
    """"""
    sql = sql_tpl % (ord(cp), font_family,
            font_family, # TODO: make this a useful entry!
            primary)
    with open('{0}sql/{1:02X}.sql'.format(TARGET_DIR,
            ord(cp) / 0x1000), 'a') as sqlfile:
        sqlfile.write(sql)


def emit_font(cp, d):
    """"""
    return True


def emit(cp, d, font_family):
    """take the path and do whatever magic is needed"""
    emit_images(cp, d)
    emit_font(cp, d)
    emit_sql(cp, font_family)
    return True


#EOF
