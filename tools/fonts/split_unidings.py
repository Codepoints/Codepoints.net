#!/usr/bin/env python
"""Fetch blocks from DB, split all_unidings.svg in single <path>s and
   store them as separate images"""


from   lxml import etree
import os
import sqlite3


SVG = ('<svg xmlns="http://www.w3.org/2000/svg" version="1.1"'
           ' viewBox="0 -784 1000 1000">'
         '<path transform="scale(1,-1)" d="{0}"/>'
       '</svg>')


def main():
    if not os.path.isdir('target/blocks'):
        os.mkdir('target/blocks')
    with open('all_unidings.svg') as svg:
        r = etree.parse(svg)
    paths = r.getroot().findall('.//svg:path', {
                'svg': 'http://www.w3.org/2000/svg' })
    conn = sqlite3.connect('../../ucd.sqlite')
    cur = conn.cursor()
    for block in cur.execute('SELECT name, first FROM blocks;').fetchall():
        name = block[0].replace(' ', '_')
        id_ = 'u%04X' % block[1]
        path = [ path for path in paths if path.get('id') == id_ ][0]
        with open('target/blocks/{}.svg'.format(name), 'wb') as img:
            img.write(SVG.format(path.get('d')))


if __name__ == "__main__":
    main()


#EOF
