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
import re
import sqlite3
import sys


stopwords = nltk.corpus.stopwords.words('english')
punctrm = re.compile(ur'[!-/:-@\[-`{-~\u2212\u201C\u201D]', re.UNICODE)
wnl = nltk.WordNetLemmatizer()

EXECUTE_DIRECTLY = True
if len(sys.argv) > 1 and sys.argv[1] == '--print':
    EXECUTE_DIRECTLY = False


def exec_sql(*sql):
    """execute or store an SQL query"""
    if EXECUTE_DIRECTLY:
        cur.execute(*sql)
    else:
        sql0 = sql[0].decode('UTF-8').replace('?', "'{}'")
        if len(sql) > 1:
            sql0 = sql0.format(*map(lambda s: isinstance(s, basestring) and s.replace("'", "''") or s, sql[1]))
        print sql0.encode('UTF-8')


def get_abstract_tokens(cp):
    """Fetch abstract for cp and split it in tokens"""
    abstract = (cur.execute("SELECT abstract FROM codepoint_abstract WHERE cp = ? AND lang = 'en'", (cp,)).fetchone() or [None])[0]
    if abstract:
        terms = BeautifulSoup(abstract).get_text()
        tokens = \
            [wnl.lemmatize(t) for t in
                set(
                    filter(lambda w: re.sub(punctrm, '', w) != '',
                        [w for w
                            in map(lambda s: s.lower(),
                                nltk.word_tokenize(terms))
                            if w not in stopwords]
                    )
                )
            ]
    else:
        tokens = []
    return tokens


def get_decomp(cp):
    """get the decomposition mapping of a codepoint"""
    dms = cur.execute("""SELECT "other" FROM codepoint_relation
                       WHERE cp = ? AND relation = 'dm' ORDER BY "order" ASC""",
                       (cp,)).fetchall()

    if len(dms) and dms[0][0] != cp:
        return reduce(lambda x, y: x + unichr(int(y[0])), dms, u'').lower()
    return None


def get_aliases(cp):
    """get all aliases of a codepoint"""
    res = cur.execute("SELECT alias FROM codepoint_alias WHERE cp = ?", (cp,))
    return map(lambda s: s[0], res.fetchall())


def get_block(cp):
    """get block name of a codepoint"""
    res = cur.execute("SELECT name FROM blocks WHERE first <= ? AND last >= ?", (cp,cp))
    blk = res.fetchone()
    if blk:
        blk = blk[0]
    return blk


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

# create the table/index for the search index
exec_sql('''
    CREATE TABLE IF NOT EXISTS search_index(
        cp INTEGER(7) REFERENCES codepoints,
        term VARCHAR(255) ,
        weight INTEGER(2) DEFAULT 1,
        INDEX search_index_term (term)
    );''')

res = cur.execute('SELECT * FROM codepoints;')

i = 0
for item in res.fetchall():
    i += 1
    cp = item['cp']

    # delete previous entries
    exec_sql(u'DELETE FROM search_index WHERE cp = ?', (cp,))

    for j, weight in (('na', 100), ('na1', 90), ('kDefinition', 50)):
        if item[j]:
            for w in re.split(r'\s+', item[j].lower()):
                exec_sql('INSERT INTO search_index (cp, term, weight) '
                    'VALUES (?, ?, ?);', (cp, w, weight))
                if '-' in w:
                    # we need this to find cps like "TAG HYPHEN-MINUS"
                    # when searching for "hyphen".
                    for w2 in w.split('-'):
                        exec_sql('INSERT INTO search_index (cp, term, weight) '
                            'VALUES (?, ?, ?);', (cp, w2, weight-20))

    for prop in item.keys():
        if (prop not in ('na', 'na1', 'kDefinition', 'cp') and
            prop is not None and item[prop] is not None):
            # all other properties get stored as foo:bar pairs, with foo
            # as property and bar as its value
            exec_sql(u'INSERT INTO search_index (cp, term, weight) '
                u'VALUES (?, ?, ?);', (cp, u'{}:{}'.format(prop, item[prop]), 50))

    for w in get_aliases(cp):
        exec_sql('INSERT INTO search_index (cp, term, weight) '
            'VALUES (?, ?, 40);', (cp, w))

    for w in get_abstract_tokens(cp):
        exec_sql('INSERT INTO search_index (cp, term, weight) '
            'VALUES (?, ?, 1);', (cp, w))

    dm = get_decomp(cp)
    if dm:
        exec_sql('INSERT INTO search_index (cp, term, weight) '
            'VALUES (?, ?, 30);', (cp, dm))

    h = '0'
    if has_confusables(cp):
        h = '1'
    exec_sql('INSERT INTO search_index (cp, term, weight) '
        'VALUES (?, ?, 50);', (cp, 'confusables:'+h))

    block = get_block(cp)
    if block:
        exec_sql('INSERT INTO search_index (cp, term, weight) '
            'VALUES (?, ?, 30);', (cp, 'blk:%s' % block))

    if i % 1000 == 0:
        print '-- U+%04X' % cp

cur.close()
conn.commit()
conn.close()
