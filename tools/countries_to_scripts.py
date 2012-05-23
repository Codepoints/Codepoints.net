#!/usr/bin/python

import csv
import json

scr = {
"AD":['Latn'],
"AE":['Arab'],
"AF":['Arab'],
"AL":['Latn'],
"AM":['Armn'],
"AN":['Latn'],
"AO":['Latn'],
"AQ":[],
"AR":['Latn'],
"AS":['Latn'],
"AT":['Latn'],
"AU":['Latn'],
"AW":['Latn'],
"AX":['Latn'],
"AZ":['Latn'],
"BA":['Cyrl','Latn'],
"BD":['Beng'],
"BE":['Latn'],
"BF":['Latn'],
"BG":['Cyrl'],
"BH":['Arab'],
"BI":['Latn'],
"BJ":['Latn'],
"BL":['Latn'],
"BN":['Latn'],
"BO":['Latn'],
"BR":['Latn'],
"BS":[],
"BT":['Tibt'],
"BW":[],
"BY":['Cyrl'],
"BZ":[],
"CA":['Latn'],
"CD":['Latn'],
"CF":['Latn'],
"CG":['Latn'],
"CH":['Latn'],
"CI":['Latn'],
"CL":['Latn'],
"CM":['Latn'],
"CN":['Arab','Hans','Latn','Mong','Tibt','Yiii'],
"CO":['Latn'],
"CP":['Latn'],
"CR":['Latn'],
"CU":['Latn'],
"CV":['Latn'],
"CY":['Grek','Latn'],
"CZ":['Latn'],
"DE":['Latn'],
"DJ":['Latn'],
"DK":['Latn'],
"DO":['Latn'],
"DZ":['Arab','Latn'],
"EA":['Latn'],
"EC":['Latn'],
"EE":['Latn'],
"EG":['Arab'],
"EH":['Arab'],
"ER":['Ethi','Latn'],
"ES":['Latn'],
"ET":['Ethi','Latn'],
"FI":['Latn'],
"FJ":['Latn'],
"FK":['Latn'],
"FM":['Latn'],
"FO":['Latn'],
"FR":['Latn'],
"GA":['Latn'],
"GB":['Latn'],
"GE":['Cyrl','Geor'],
"GF":['Latn'],
"GH":['Latn'],
"GL":['Latn'],
"GM":[],
"GN":['Latn'],
"GP":['Latn'],
"GQ":['Latn'],
"GR":['Grek'],
"GT":['Latn'],
"GU":['Latn'],
"GW":['Latn'],
"GY":['Latn'],
"HK":['Hant'],
"HN":['Latn'],
"HR":['Latn'],
"HT":['Latn'],
"HU":['Latn'],
"IC":['Latn'],
"ID":['Latn'],
"IE":['Latn'],
"IL":['Hebr'],
"IN":['Arab','Beng','Deva','Gujr','Guru','Knda','Latn','Mlym','Orya','Taml','Telu'],
"IQ":['Arab'],
"IR":['Arab'],
"IS":['Latn'],
"IT":['Latn'],
"JM":['Latn'],
"JO":['Arab'],
"JP":['Jpan'],
"KE":['Latn'],
"KG":['Cyrl'],
"KH":['Khmr'],
"KI":['Latn'],
"KM":['Arab','Latn'],
"KP":['Kore'],
"KR":['Kore'],
"KW":['Arab'],
"KZ":['Cyrl'],
"LA":['Laoo'],
"LB":['Arab'],
"LI":['Latn'],
"LK":['Sinh'],
"LR":['Latn','Vaii'],
"LS":['Latn'],
"LT":['Latn'],
"LU":['Latn'],
"LV":['Latn'],
"LY":['Arab'],
"MA":['Arab','Latn','Tfng'],
"MC":['Latn'],
"MD":['Latn'],
"ME":['Latn'],
"MF":['Latn'],
"MG":['Latn'],
"MH":['Latn'],
"MK":['Cyrl','Latn'],
"ML":['Latn'],
"MM":['Mymr'],
"MN":['Cyrl'],
"MO":['Hant'],
"MQ":['Latn'],
"MR":['Arab'],
"MT":['Latn'],
"MU":['Latn'],
"MV":['Thaa'],
"MW":['Latn'],
"MX":['Latn'],
"MY":['Latn'],
"MZ":['Latn'],
"NA":['Latn'],
"NC":['Latn'],
"NE":['Latn'],
"NG":['Arab','Latn'],
"NI":['Latn'],
"NL":['Latn'],
"NO":['Latn'],
"NP":['Deva'],
"NR":['Latn'],
"NU":['Latn'],
"NZ":['Latn'],
"OM":['Arab'],
"PA":['Latn'],
"PE":['Latn'],
"PF":['Latn'],
"PG":['Latn'],
"PH":['Latn'],
"PK":['Arab'],
"PL":['Latn'],
"PM":['Latn'],
"PR":['Latn'],
"PS":['Arab'],
"PT":['Latn'],
"PW":['Latn'],
"PY":['Latn'],
"QA":['Arab'],
"RE":['Latn'],
"RO":['Latn'],
"RS":['Cyrl','Latn'],
"RU":['Cyrl'],
"RW":['Latn'],
"SA":['Arab'],
"SB":[],
"SC":['Latn'],
"SD":['Arab','Latn'],
"SE":['Latn'],
"SI":['Latn'],
"SJ":['Latn'],
"SK":['Latn'],
"SL":[],
"SM":['Latn'],
"SN":['Latn'],
"SO":['Latn'],
"SR":['Latn'],
"SS":['Latn'],
"ST":['Latn'],
"SV":['Latn'],
"SY":['Arab','Latn'],
"SZ":[],
"TD":['Latn'],
"TF":[],
"TG":['Latn'],
"TH":['Thai'],
"TJ":['Cyrl'],
"TK":['Latn'],
"TL":['Latn'],
"TM":['Latn'],
"TN":['Arab','Latn'],
"TO":['Latn'],
"TR":['Latn'],
"TT":['Latn'],
"TV":['Latn'],
"TW":['Hant','Latn'],
"TZ":['Latn'],
"UA":['Cyrl'],
"UG":['Latn'],
"US":['Cher','Latn'],
"UY":['Latn'],
"UZ":['Cyrl'],
"VA":['Latn'],
"VE":['Latn'],
"VN":['Latn'],
"VU":['Latn'],
"WF":['Latn'],
"WS":['Latn'],
"XK":[],
"YE":['Arab'],
"YT":['Latn'],
"ZA":['Latn'],
"ZM":['Latn'],
"ZW":['Latn'],
}

scr["AQ"].extend([])
scr["BS"].extend(["Latn"])
scr["BW"].extend(["Latn"])
scr["BZ"].extend(["Latn"])
scr["GM"].extend(["Latn", "Arab"])
scr["SB"].extend(["Latn"])
scr["SL"].extend(["Latn"])
scr["SZ"].extend(["Latn"])
scr["TF"].extend(["Latn"])
scr["XK"].extend(["Latn", "Cyrl"])

oscr = {
"AF":["Khar"],
"AL":["Elba"],
"AU":["Shaw"],
"BD":["Mroo","Sylo","Cakm"],
"BT":["Lepc","Limb"],
"CA":["Cans","Shaw"],
"CI":["Nkoo"],
"CM":["Bamu"],
"CN":["Hani","Lisu","Nshu","Phag","Plrd","Talu","Tang","Tavt"],
"CY":["Cprt"],
"EG":["Egyp","Copt"],
"ER":["Sarb"],
"ET":["Sarb"],
"GN":["Nkoo"],
"GR":["Lina","Linb"],
"HU":["Hung"],
"ID":["Bali","Batk","Bugi","Sund","Rjng","Java"],
"IE":["Latg","Ogam","Shaw"],
"IL":["Samr","Armi"],
"IM":["Latg","Ogam","Shaw"],
"IN":["Brah","Khar","Kthi","Mtei","Olck","Saur","Shrd","Sora","Takr","Lepc","Limb","Cakm"],
"IQ":["Narb","Mand","Armi","Xsux"],
"IR":["Avst","Phli","Prti","Xpeo","Mand","Armi","Mani"],
"IT":["Ital"],
"JO":["Narb","Nbat"],
"JP":["Hira","Hrkt","Kana"],
"KG":["Orkh"],
"KH":["Cham"],
"KP":["Hang"],
"KR":["Hang"],
"KZ":["Orkh"],
"LA":["Lana","Tale","Tavt"],
"LB":["Phnx"],
"LR":["Bass"],
"ML":["Nkoo"],
"MM":["Kali","Lisu","Mroo","Tale"],
"MN":["Phag","Orkh"],
"MY":["Java"],
"NC":["Java"],
"NP":["Lepc","Limb"],
"NZ":["Shaw"],
"PH":["Buhd","Hano","Tagb","Tglg"],
"PK":["Khar","Shrd","Takr"],
"PS":['Armi'],
"SA":["Narb"],
"SD":["Merc","Mero"],
"SO":["Osma"],
"SR":["Java"],
"SS":["Merc","Mero"],
"SY":["Narb","Palm","Syrc","Ugar","Armi","Xsux"],
"TH":["Kali","Lana","Tale","Tavt"],
"TJ":["Orkh"],
"TM":["Orkh"],
"TR":["Cari","Lyci","Lydi","Armi"],
"TW":["Bopo"],
"UK":["Ogam","Shaw"],
"US":["Dsrt","Shaw"],
"UZ":["Orkh"],
"VN":["Cham","Tale","Tavt"],
"YE":["Sarb"],
}

others = [
    "Brai", "Dupl", "Latf", "Qaai",
    "Zinh", "Zmth", "Zsym", "Zyyy", "Zzzz",
]

import copy
xall = copy.copy(others)
for x in scr.values():
    xall.extend(x)
for x in oscr.values():
    xall.extend(x)
xall = list(set(xall))
xall.sort()

import sqlite3
conn = sqlite3.connect('../ucd.sqlite')
cur = conn.cursor()
cur.execute('select sc from script_abstract')
yall = []
for x in cur:
    yall.append(str(x[0]))
for x in xall:
    try:
        yall.remove(x)
    except ValueError:
        pass

print ' '.join(yall)

with open('world-countries.json') as p:
    props = json.load(p)

feats = props['features']

with open('countrynames.csv') as ff:
    f = csv.reader(ff, delimiter=";")
    result = []

    f.next()
    for line in f:
        if line[0][0] != "#":
            scripts = []
            oldscripts = []
            if line[0] in scr:
                scripts.extend(scr[line[0]])
            if line[0] in oscr:
                oldscripts.extend(oscr[line[0]])
            geo = {}
            for feat in feats:
                if feat['id'] == line[1].strip():
                    geo = feat['geometry']
            result.append({
                'type': "Feature",
                'id': line[0],
                'properties': {
                    'name': line[3].strip(),
                    'id3': line[1].strip(),
                    'scripts': scripts,
                    'oldscripts': oldscripts,
                    },
                'geometry': geo,
            })

    result = {"type":"FeatureCollection","features":result}

    json.dump(result, open('countrynames.json', 'wb'), separators=(',',':'))
    #json.dump(result, open('countrynames.json', 'wb'), sort_keys=True, indent=4)

