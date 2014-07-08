#!/usr/bin/python

"""Read a large SQL file and apply it statement by statement

This seems faster than with the CLI sqlite3 client. Apart from that,
we can simply output a counter to tell us the advance."""

import os
import sqlite3
import sys

if len(sys.argv) != 3:
    raise ValueError("Need exactly two args: insert.py sql db")
sqlfile = sys.argv[1]
db = sys.argv[2]
if sqlfile is "-":
    sqlfile = sys.stdin
elif not os.path.isfile(sqlfile):
    raise IOError("SQL file not found")
else:
    sqlfile = open(sqlfile, 'r')
if not os.path.isfile(db):
    raise IOError("Database not found")

conn = sqlite3.connect(db)
cur = conn.cursor()

sql = sqlfile.read()
inserts = sql.split(";\n")

sys.stdout.write(10*' ')
sys.stdout.flush()
for i, insert in enumerate(inserts):
    try:
        cur.execute(insert+';')
    except sqlite3.OperationalError, e:
        print insert
        raise
    if i > 0 and i % 1000 == 0:
        sys.stdout.write(10*'\b' + '%10s' % i)
        sys.stdout.flush()
sys.stdout.write('\n')

cur.close()
conn.close()
