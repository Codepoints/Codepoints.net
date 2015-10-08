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
import MySQLdb
import sys
import ConfigParser


config = ConfigParser.RawConfigParser()
config.read((dirname(dirname(__file__)) or '..')+'/db.conf')


stopwords = nltk.corpus.stopwords.words('english')
punctrm = re.compile(ur'[!-/:-@\[-`{-~\u2212\u201C\u201D]', re.UNICODE)
wnl = nltk.WordNetLemmatizer()

EXECUTE_DIRECTLY = True
if len(sys.argv) > 1 and sys.argv[1] == '--print':
    EXECUTE_DIRECTLY = False


def sql_convert(s):
    """"""
    if isinstance(s, basestring):
        s = "'"+s.replace("\\", "\\\\").replace("'", "\\'")+"'"
        if isinstance(s, str):
            s = s.decode('utf-8')
    elif isinstance(s, long):
        s = int(s)
    return s


def exec_sql(*sql):
    """execute or store an SQL query"""
    if EXECUTE_DIRECTLY:
        cur.execute(*sql)
    else:
        sql0 = sql[0].decode('UTF-8')
        if len(sql) > 1:
            params = tuple(map(sql_convert, sql[1]))
            sql0 = sql0 % params
        print sql0.encode('UTF-8')


def get_abstract_tokens(cp):
    """Fetch abstract for cp and split it in tokens"""
    cur.execute("SELECT abstract FROM codepoint_abstract WHERE cp = %s AND lang = 'en'", (cp,))
    abstract = (cur.fetchone() or {'abstract':None})['abstract']
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
    cur.execute("""SELECT `other` FROM codepoint_relation
                   WHERE cp = %s AND relation = 'dm' ORDER BY `order` ASC""",
                   (cp,))
    dms = cur.fetchall()

    if len(dms) and dms[0]['other'] != cp:
        return reduce(lambda x, y: x + unichr(int(y['other'])), dms, u'').lower()
    return None


def get_aliases(cp):
    """get all aliases of a codepoint"""
    cur.execute("SELECT alias FROM codepoint_alias WHERE cp = %s", (cp,))
    return map(lambda s: s['alias'], cur.fetchall())


def get_block(cp):
    """get block name of a codepoint"""
    cur.execute("SELECT name FROM blocks WHERE first <= %s AND last >= %s", (cp,cp))
    blk = cur.fetchone()
    if blk:
        blk = blk['name']
    return blk


def get_script(cp):
    """get script of a codepoint"""
    cur.execute("SELECT sc FROM codepoint_script WHERE cp = %s", (cp,))
    sc = cur.fetchone()
    if sc:
        sc = sc['sc']
    return sc


def has_confusables(cp):
    """whether the CP has any confusables"""
    cur.execute('''
            SELECT COUNT(*) AS c
               FROM codepoint_confusables
              WHERE codepoint_confusables.cp = %s
                 OR codepoint_confusables.other = %s''', (cp,cp))
    return cur.fetchone()['c']


conn = MySQLdb.connect(
        host='localhost',
        user=config.get('clientreadonly', 'user'),
        passwd=config.get('clientreadonly', 'password'),
        db=config.get('clientreadonly', 'database'))
conn.set_character_set('utf8')
cur = conn.cursor(MySQLdb.cursors.DictCursor)
cur.execute('SET NAMES utf8;')
cur.execute('SET CHARACTER SET utf8;')
cur.execute('SET character_set_connection=utf8;')

# create the table/index for the search index
exec_sql('''
    CREATE TABLE IF NOT EXISTS search_index(
        cp INTEGER(7) REFERENCES codepoints,
        term VARCHAR(255) ,
        weight INTEGER(2) DEFAULT 1,
        INDEX search_index_term (term)
    );''')

cur.execute('SELECT * FROM codepoints;')
all_cps = cur.fetchall()

i = 0
for item in all_cps:
    i += 1
    cp = item['cp']

    # delete previous entries
    exec_sql(u'DELETE FROM search_index WHERE cp = %s;', (cp,))

    exec_sql(u'INSERT INTO search_index (cp, term, weight) '
             u'VALUES (%s, %s, 80);', (cp, u'int:{}'.format(cp)))

    for j, weight in (('na', 100), ('na1', 90), ('kDefinition', 50)):
        if item[j]:
            for w in re.split(r'\s+', item[j].lower()):
                exec_sql('INSERT INTO search_index (cp, term, weight) '
                    'VALUES (%s, %s, %s);', (cp, w, weight))
                if '-' in w:
                    # we need this to find cps like "TAG HYPHEN-MINUS"
                    # when searching for "hyphen".
                    for w2 in w.split('-'):
                        exec_sql('INSERT INTO search_index (cp, term, weight) '
                            'VALUES (%s, %s, %s);', (cp, w2, weight-20))

    for prop in item.keys():
        if (prop not in ('na', 'na1', 'kDefinition', 'cp') and
            prop is not None and item[prop] is not None):
            # all other properties get stored as foo:bar pairs, with foo
            # as property and bar as its value
            _i = item[prop]
            if type(_i) is str:
                _i = _i.decode('utf-8')
            elif type(_i) is long:
                _i = int(_i)
            exec_sql(u'INSERT INTO search_index (cp, term, weight) '
                u'VALUES (%s, %s, %s);', (cp, u'%s:%s' % (prop, _i), 50))
            if prop == 'scx':
                scx = _i.split(u' ')
                for sc in scx:
                    # add for search by script ("sc:%"), but with lesser weight
                    # than true sc below:
                    exec_sql(u'INSERT INTO search_index (cp, term, weight) '
                        u'VALUES (%s, %s, 25);', (cp, u'sc:{}'.format(sc)))

    for w in get_aliases(cp):
        exec_sql('INSERT INTO search_index (cp, term, weight) '
            'VALUES (%s, %s, 40);', (cp, w))

    for w in get_abstract_tokens(cp):
        exec_sql('INSERT INTO search_index (cp, term, weight) '
            'VALUES (%s, %s, 1);', (cp, w))

    dm = get_decomp(cp)
    if dm:
        exec_sql('INSERT INTO search_index (cp, term, weight) '
            'VALUES (%s, %s, 30);', (cp, dm))

    h = '0'
    if has_confusables(cp):
        h = '1'
    exec_sql('INSERT INTO search_index (cp, term, weight) '
        'VALUES (%s, %s, 50);', (cp, 'confusables:'+h))

    block = get_block(cp)
    if block:
        exec_sql('INSERT INTO search_index (cp, term, weight) '
            'VALUES (%s, %s, 30);', (cp, 'blk:%s' % block))

    script = get_script(cp)
    if script:
        exec_sql('INSERT INTO search_index (cp, term, weight) '
            'VALUES (%s, %s, 50);', (cp, 'sc:%s' % script))

    if i % 1000 == 0:
        print '-- U+%04X' % cp

cur.close()
conn.commit()
conn.close()
