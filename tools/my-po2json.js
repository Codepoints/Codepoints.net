#!/usr/bin/env node

var po2json = require('po2json'),
    lang = process.argv[2],
    path = require('path'),
    fs = require('fs'),
    jsondata = '', file, target;

file = 'codepoints.net/locale/'+lang+'/LC_MESSAGES/js.po';
target = 'codepoints.net/static/locale/'+lang+'.js';

jsondata = po2json.parseSync(file);
jsondata = jsondata[path.basename(file, '.po')];
delete(jsondata['']);
var key;
for (key in jsondata) {
    if (jsondata.hasOwnProperty(key)) {
        jsondata[key] = jsondata[key][1];
    }
}
fs.writeFile(target, 'gettext=(typeof gettext!=="undefined")?gettext:{catalog:{}};gettext.catalog.'+lang+'='+JSON.stringify(jsondata));
