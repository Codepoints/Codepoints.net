"""Collect all types of info for font processing"""


import json
import logging
import os.path as op
import sqlite3

from   process.settings import TARGET_DIR, LOAD_CACHE, BLOCK_SPLIT_SIZE


logger = logging.getLogger('codepoint.fonts')


def get_blocks():
    """get all blocks and their codepoints"""
    conn = sqlite3.connect('../../ucd.sqlite')
    cur = conn.cursor()
    blocks = []
    for x in cur.execute('SELECT name, first, last FROM blocks;').fetchall():
        if (x[2] - x[1]) > BLOCK_SPLIT_SIZE:
            logger.warning('Skipping block {} due to size ({})'.format(x[0], x[2] - x[1]))
            continue
        blocks.append([ x[0].replace(' ', '_'), x[1], x[2] ])
    return blocks


def get_all_codepoints():
    """fetch _all_ codepoints from db"""
    conn = sqlite3.connect('../../ucd.sqlite')
    cur = conn.cursor()
    return [
        [ int(x[0]), x[1] ]
        for x in
            cur.execute('SELECT cp, blk FROM codepoints').fetchall() ]


def load_cache():
    """load already handled codepoints"""
    if not LOAD_CACHE or not op.isfile(TARGET_DIR+'cache/cache.json'):
        return {}
    with open(TARGET_DIR+'cache/cache.json') as _file:
        cache = json.load(_file)
    # cache is a list of lists. Restore dict w/ numeric keys
    return dict(cache)


#EOF
