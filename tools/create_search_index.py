#!/usr/bin/python
"""
Create the search index table and fill it

The search index is a simple table that contains codepoints and their
search terms: name, abstract, ... We create it from the other information
stored in the ucd.sqlite database. Some, like abstract and kDefinition,
get split, stopwords removed and inserted in pieces.

The terms are weighted. That means, each term has an associated number
representing its importance for the codepoint. Names get the highest weight,
words in the Wikipedia abstract the lowest. This ensures, that the search
for "ox" finds U+1F402 OX before any other codepoint that happens to
relate to oxen.
"""

from bs4 import BeautifulSoup
import nltk
from os.path import dirname
import sqlite3
import re


stopwords = nltk.corpus.stopwords.words('english')
punctrm = re.compile(ur'[!-/:-@\[-`{-~\u2212\u201C\u201D]', re.UNICODE)

EXECUTE_DIRECTLY = True
SQL = ''


def exec_sql(*sql):
    """execute or store an SQL query"""
    if EXECUTE_DIRECTLY:
        cur.execute(*sql)
    else:
        sql0 = sql[0]
        for x in sql[1:]:
            sql0 = sql0.replace('?', x, 1)
        SQL += "\n" + sql0


def get_abstract_tokens(cp):
    """Fetch abstract for cp and split it in tokens"""
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
    """get all aliases of a codepoint"""
    res = cur.execute("SELECT alias FROM codepoint_alias WHERE cp = ?", (cp,))
    return map(lambda s: s[0], res.fetchall())


def has_confusables(cp):
    """whether the CP has any confusables"""
    res = cur.execute('''
            SELECT COUNT(*)
               FROM codepoint_confusables
              WHERE codepoint_confusables.cp = ?
                 OR codepoint_confusables.other = ?''', (cp,cp))
    return res.fetchone()[0]


conn = sqlite3.connect((dirname(__file__) or '.')+'/../ucd.sqlite')
conn.row_factory = sqlite3.Row
cur = conn.cursor()

# drop existing index. We build it from scratch
exec_sql('DROP INDEX IF EXISTS search_index_term;')
exec_sql('DROP TABLE IF EXISTS search_index;')

# create the table/index for the search index
exec_sql('''
    CREATE TABLE
        search_index (
            cp INTEGER(7) REFERENCES codepoints,
            term TEXT,
            weight INTEGER(2) DEFAULT 1
        )''')
exec_sql('''
    CREATE INDEX
        search_index_term
        ON search_index ( term )''')

res = cur.execute('SELECT * FROM codepoints')

i = 0
for item in res.fetchall():
    i += 1
    cp = item['cp']

    for j, weight in (('na', 100), ('na1', 90), ('kDefinition', 50)):
        if item[j]:
            for w in re.split(r'\s+', item[j].lower()):
                exec_sql('''
                INSERT INTO search_index (cp, term, weight)
                VALUES (?, ?, ?);''', (cp, w, weight))

    for prop in item.keys():
        if (prop not in ('na', 'na1', 'kDefinition', 'cp') and
            prop is not None):
            # all other properties get stored as foo:bar pairs, with foo
            # as property and bar as its value
            exec_sql(u'''
            INSERT INTO search_index (cp, term, weight)
            VALUES (?, ?, ?);''', (cp, u'{}:{}'.format(prop, item[prop]), 50))

    for w in get_aliases(cp):
        exec_sql('''
        INSERT INTO search_index (cp, term, weight)
        VALUES (?, ?, 40);''', (cp, w))

    for w in get_abstract_tokens(cp):
        exec_sql('''
        INSERT INTO search_index (cp, term, weight)
        VALUES (?, ?, 1);''', (cp, w))

    h = '0'
    if has_confusables(cp):
        h = '1'
    exec_sql('''
        INSERT INTO search_index (cp, term, weight)
        VALUES (?, ?, 50);''', (cp, 'confusables:'+h))

    if EXECUTE_DIRECTLY and i % 1000 == 0:
        print 'U+%04X' % cp

cur.close()
conn.commit()
conn.close()

if SQL:
    print SQL
