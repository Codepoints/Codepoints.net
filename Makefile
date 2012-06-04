

all:

.PHONY: all css js dist

dist: css js
	mkdir $@
	cp -r .htaccess humans.txt index.php lib opensearch.xml robots.txt static ucd.sqlite views $@
	sed -i 's/define(.CP_DEBUG., .);/define('"'CP_DEBUG'"', 0);/' $@/index.php

css: static/css/codepoints.css static/css/ie.css

static/css/codepoints.css static/css/ie.css: static/sass/*.scss
	compass compile

js: static/js/_.js

static/js/_.js: static/js/jquery.js static/js/jquery.ui.js static/js/codepoints.js
	cat $^ | uglifyjs > $@

