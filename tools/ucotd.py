#!/usr/bin/python

import sqlite3
import json


with open('ucotd.json') as f:
    ucotd = json.load(f)

tpl = ('INSERT OR REPLACE INTO dailycp ("date", cp, comment) '
       'VALUES (?, ?, ?);')

conn = sqlite3.connect('../codepoints.net/ucd.sqlite')
cur = conn.cursor()

with open('ucotd.sql') as f:
    cur.execute(f.read())

for date, data in ucotd.iteritems():
    if isinstance(data, list) and data[0] and data[0] != "0000":
        cur.execute(tpl, (date, int(data[0], 16), data[2]))

conn.commit()
cur.close()
conn.close()

