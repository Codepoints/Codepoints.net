#!/usr/bin/env python
"""Fetch blocks from DB, split all_unidings.svg in single <path>s and
   store them as separate images"""


import gzip
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
        for path in paths:
            path_id = path.get('id')
            if path_id in [ "u0000", "u0080" ]:
                continue
            if path_id == "u0020":
                path_id = "u0000"
            if path_id == "u00A0":
                path_id = "u0080"
            if (path_id == id_):
                with gzip.open('target/blocks/{}.svgz'.format(name), 'wb') as img:
                    img.write(SVG.format(path.get('d')))
                break


if __name__ == "__main__":
    main()


#EOF
