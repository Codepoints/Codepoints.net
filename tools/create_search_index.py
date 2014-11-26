#!/usr/bin/python

from bs4 import BeautifulSoup
import nltk
from os.path import dirname
import sqlite3
import re


stopwords = nltk.corpus.stopwords.words('english')
punctrm = re.compile(ur'[!-/:-@\[-`{-~\u2212\u201C\u201D]', re.UNICODE)


def get_abstract_tokens(cp):
    """"""
    abstract = (cur.execute("SELECT abstract FROM codepoint_abstract WHERE cp = ? AND lang = 'en'", (cp,)).fetchone() or [None])[0]
    if abstract:
        terms = BeautifulSoup(abstract).get_text()
        tokens = \
            list(
                set(
                    filter(lambda w: re.sub(punctrm, '', w) != '',
                        [w for w
                            in map(lambda s: s.lower(),
                                nltk.word_tokenize(terms))
                            if w not in stopwords])))
    else:
        tokens = ['']
    return tokens


def get_aliases(cp):
    """"""
    res = cur.execute("SELECT alias FROM codepoint_alias WHERE cp = ?", (cp,))
    return map(lambda s: s[0], res.fetchall())


conn = sqlite3.connect((dirname(__file__) or '.')+'/../ucd.sqlite')
cur = conn.cursor()

cur.execute('DROP INDEX IF EXISTS search_index_term;')
cur.execute('DROP TABLE IF EXISTS search_index;')

cur.execute('''
    CREATE TABLE
        search_index (
            cp INTEGER(7) REFERENCES codepoints,
            term TEXT,
            weight INTEGER(2) DEFAULT 1
        )''')
cur.execute('''
    CREATE INDEX
        search_index_term
        ON search_index ( term )''')

res = cur.execute('SELECT cp, na, na1, kDefinition FROM codepoints')

i = 0
for item in res.fetchall():
    i += 1
    item = list(item)
    cp = item.pop(0)
    for j, weight in enumerate([100,90,50]):
        if item[j]:
            for w in re.split(r'\s+', item[j].lower()):
                cur.execute('''
                INSERT INTO search_index (cp, term, weight)
                VALUES (?, ?, ?);''', (cp, w, weight))
    for w in get_aliases(cp):
        cur.execute('''
        INSERT INTO search_index (cp, term, weight)
        VALUES (?, ?, 40);''', (cp, w))
    for w in get_abstract_tokens(cp):
        cur.execute('''
        INSERT INTO search_index (cp, term, weight)
        VALUES (?, ?, 1);''', (cp, w))
    if i == 1000:
        print 'U+%04X' % cp

cur.close()
conn.commit()
conn.close()
