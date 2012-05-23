#!/usr/bin/python


import codecs
import urllib
import urllib2
import json
import gzip
import sys
from StringIO import StringIO


def main():
    scripts = [
        ('Arab', 'Arabic_script'),
        ('Armi', 'Aramaic_script'),
        ('Armn', 'Armenian_alphabet'),
        ('Avst', 'Avestan_script'),
        ('Bali', 'Balinese_script'),
        ('Bamu', 'Bamum_script'),
        ('Bass', 'Bassa_script'),
        ('Batk', 'Batak_script'),
        ('Beng', 'Bengali_script'),
        ('Bopo', 'Bopomofo'),
        ('Brah', 'Brahmi_script'),
        ('Brai', 'Braille'),
        ('Bugi', 'Buginese_script'),
        ('Buhd', 'Buhid_script'),
        ('Cakm', 'Chakma_alphabet'),
        ('Cans', 'Canadian_Aboriginal_syllabics'),
        ('Cari', 'Carian_script'),
        ('Cham', 'Cham_script'),
        ('Cher', 'Cherokee_syllabary'),
        ('Copt', 'Coptic_alphabet'),
        ('Cprt', 'Cypriot_syllabary'),
        ('Cyrl', 'Cyrillic_script'),
        ('Deva', 'Devanagari'),
        ('Dsrt', 'Deseret_alphabet'),
        ('Dupl', 'Duployan_shorthand'),
        ('Egyp', 'Egyptian_hieroglyphs'),
        ('Elba', 'Elbasan_alphabet'),
        ('Ethi', 'Ge%27ez_abugida'),
        ('Geor', 'Georgian_alphabet'),
        ('Glag', 'Glagolitic_alphabet'),
        ('Goth', 'Gothic_alphabet'),
        ('Grek', 'Greek_alphabet'),
        ('Gujr', 'Gujarati_script'),
        ('Guru', 'Gurmukhi_script'),
        ('Hang', 'Hangul'),
        ('Hani', 'Han_script'),
        ('Hano', 'Hanun%C3%B3%27o_script'),
        ('Hans', 'Simplified_Chinese_characters'),
        ('Hant', 'Traditional_Chinese_characters'),
        ('Hebr', 'Hebrew_alphabet'),
        ('Hira', 'Hiragana'),
        ('Hrkt', 'Hiragana_or_Katakana'),
        ('Hung', 'Old_Hungarian_script'),
        ('Ital', 'Old_Italic_script'),
        ('Java', 'Javanese_script'),
        ('Jpan', 'Japanese_writing_system'),
        ('Kali', 'Kayah_Li_script'),
        ('Kana', 'Katakana'),
        ('Khar', 'Kharosthi'),
        ('Khmr', 'Khmer_script'),
        ('Knda', 'Kannada_script'),
        ('Kore', 'Korean_writing_system'),
        ('Kthi', 'Kaithi'),
        ('Lana', 'Tai_Tham_script'),
        ('Laoo', 'Lao_script'),
        ('Latf', 'Fraktur'),
        ('Latg', 'Gaelic_type'),
        ('Latn', 'Latin_script'),
        ('Lepc', 'Lepcha_script'),
        ('Limb', 'Limbu_script'),
        ('Lina', 'Linear_A'),
        ('Linb', 'Linear_B'),
        ('Lisu', 'Fraser_alphabet'),
        ('Lyci', 'Lycian_script'),
        ('Lydi', 'Lydian_script'),
        ('Mand', 'Mandaic_alphabet'),
        ('Mani', 'Manichaean_script'),
        ('Merc', 'Meroitic_alphabet'),
        ('Mero', 'Meroitic_alphabet'),
        ('Mlym', 'Malayalam_script'),
        ('Mong', 'Classical_Mongolian_alphabet'),
        ('Mroo', 'Mro_script'),
        ('Mtei', 'Meitei_Mayek_script'),
        ('Mymr', 'Burmese_alphabet'),
        ('Narb', 'Old_North_Arabian_alphabet'),
        ('Nbat', 'Nabataean_alphabet'),
        ('Nkoo', 'N%27Ko_alphabet'),
        ('Nshu', 'N%C3%BCshu_script'),
        ('Ogam', 'Ogham'),
        ('Olck', 'Ol_Chiki_script'),
        ('Orkh', 'Old_Turkic_script'),
        ('Orya', 'Oriya_script'),
        ('Osma', 'Osmanya_script'),
        ('Palm', 'Palmyrene_script'),
        ('Phag', '%27Phags-pa_script'),
        ('Phli', 'Inscriptional_Pahlavi'),
        ('Phnx', 'Phoenician_alphabet'),
        ('Plrd', 'Pollard_script'),
        ('Prti', 'Inscriptional_Parthian'),
        ('Qaai', 'Scripts_in_Unicode#Common_and_inherited_scripts'),
        ('Rjng', 'Rejang_script'),
        ('Runr', 'Runic_alphabet'),
        ('Samr', 'Samaritan_script'),
        ('Sarb', 'Old_South_Arabian_alphabet'),
        ('Saur', 'Saurashtra_script'),
        ('Shaw', 'Shavian_alphabet'),
        ('Shrd', '%C5%9A%C4%81rad%C4%81_script'),
        ('Sinh', 'Sinhala_script'),
        ('Sora', 'Sorang_Sompeng_alphabet'),
        ('Sund', 'Sundanese_script'),
        ('Sylo', 'Syloti_Nagri'),
        ('Syrc', 'Syriac_alphabet'),
        ('Tagb', 'Tagbanwa_script'),
        ('Takr', 'Takri_script'),
        ('Tale', 'Tai_Le_script'),
        ('Talu', 'New_Tai_Lue_script'),
        ('Taml', 'Tamil_script'),
        ('Tang', 'Tangut_script'),
        ('Tavt', 'Tai_Viet_script'),
        ('Telu', 'Telugu_script'),
        ('Tfng', 'Tifinagh'),
        ('Tglg', 'Baybayin'),
        ('Thaa', 'Thaana'),
        ('Thai', 'Thai_script'),
        ('Tibt', 'Tibetan_script'),
        ('Ugar', 'Ugaritic_alphabet'),
        ('Vaii', 'Vai_syllabary'),
        ('Xpeo', 'Old_Persian_cuneiform'),
        ('Xsux', 'Cuneiform'),
        ('Yiii', 'Yi_script'),
        ('Zinh', 'ISO_15924#Special_codes'),
        ('Zmth', 'Mathematical_notation'),
        ('Zsym', 'Symbols'),
        ('Zyyy', 'ISO_15924#Special_codes'),
        ('Zzzz', 'ISO_15924#Special_codes'),
    ]
    sqlfile = codecs.open('scripts_wp.sql', 'w', 'utf-8')
    sqlfile.write("""CREATE TABLE script_abstract (
        sc TEXT(4),
        abstract TEXT,
        src TEXT(255)
    );\n""")
    sqltpl = u"INSERT INTO script_abstract ( sc, abstract, src ) VALUES ( '%s', '%s', '%s' );\n"
    c = len(scripts)
    for i, sc in enumerate(scripts):
        s = "%6s / %s" % (i, c)
        url = sc[1].split("#")[0]
        sys.stdout.write(s)
        sys.stdout.flush()
        req = urllib2.urlopen('http://en.wikipedia.org/w/api.php'+
                    '?action=query&redirects&format=json&prop=extracts'+
                    '&exintro&titles=%s' % sc[1])
        data = json.loads(req.read())
        if "query" in data:
            if "pages" in data["query"]:
                p = data["query"]["pages"]
                k = p.keys()[0]
                abstract = p[p.keys()[0]]["extract"]
                sqlfile.write(sqltpl % ( sc[0],
                                         abstract.replace(u"'", u"''"),
                                         'http://en.wikipedia.org/wiki/'+sc[1]))
        sys.stdout.write(len(s) * '\b')
        sys.stdout.flush()


if __name__ == '__main__':
    main()


