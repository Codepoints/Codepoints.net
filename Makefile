

JS_SRC = $(wildcard dev/js/*.js)
JS_TARGET = $(patsubst dev/js/%.js,static/js/%.js,$(JS_SRC))
all: ucotd css js

.PHONY: all css js dist clean ucotd

clean:
	-rm -fr dist

dist: ucotd css js
	mkdir $@
	cp -r .htaccess humans.txt index.php lib opensearch.xml robots.txt static ucd.sqlite views $@
	sed -i 's/define(.CP_DEBUG., .);/define('"'CP_DEBUG'"', 0);/' $@/index.php

css: static/css/codepoints.css static/css/ie.css

static/css/codepoints.css static/css/ie.css: static/sass/*.scss
	compass compile

js: static/js/_.js $(JS_TARGET)

static/js/_.js: static/js/jquery.js static/js/jquery.ui.js \
                static/js/webfont.js \
                static/js/jquery.cachedajax.js static/js/jquery.tooltip.js \
                static/js/jquery.glossary.js static/js/codepoints.js
	cat $^ | uglifyjs > $@

$(JS_TARGET): static/js/%.js: dev/js/%.js
	uglifyjs $^ > $@

ucotd: tools/ucotd.*
	cd tools; \
	python ucotd.py

ucd.sqlite: ucotd tools/scripts.sql tools/scripts_wp.sql tools/fonts/*_insert.sql
	sqlite3 $@ <tools/scripts.sql
	sqlite3 $@ <tools/scripts_wp.sql
	sqlite3 $@ <tools/fonts/*_insert.sql

