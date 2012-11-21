

JS_SRC = $(wildcard src/js/*.js)
JS_ALL = $(wildcard src/js*/*.js)
PHP_ALL = $(shell find . -type f -name \*.php)
JS_TARGET = $(patsubst src/js/%.js,static/js/%.js,$(JS_SRC))
PYTHON := python
all: test ucotd css js cachebust

.PHONY: all css js dist clean ucotd cachebust l10n test

clean:
	-rm -fr dist

dist: ucotd css js
	mkdir $@
	cp -r .htaccess humans.txt index.php lib opensearch.xml robots.txt static ucd.sqlite views $@
	sed -i 's/define(.CP_DEBUG., .);/define('"'CP_DEBUG'"', 0);/' $@/index.php

css: static/css/codepoints.css static/css/ie.css

static/css/codepoints.css static/css/ie.css: src/sass/*.scss
	compass compile

js: static/js/_.js static/js/embedded.js $(JS_TARGET)

static/js/_.js: src/js_embed/jquery.js src/js_embed/jquery.ui.js \
                src/js_embed/webfont.js \
                src/js_embed/jquery.cachedajax.js src/js_embed/jquery.tooltip.js \
                src/js_embed/jquery.glossary.js src/js_embed/codepoints.js
	cat $^ | uglifyjs > $@

static/js/embedded.js: dev/js_embed/jquery.js dev/js_embed/webfont.js \
                       dev/js_embed/load_font.js
	cat $^ | uglifyjs > $@

cachebust: $(JS_ALL) static/css/*.css
	sed -i '/^define(.CACHE_BUST., .\+.);$$/s/.*/define('"'CACHE_BUST', '"$$(cat $^ | sha1sum | awk '{ print $$1 }')"');/" index.php

$(JS_TARGET): static/js/%.js: src/js/%.js
	uglifyjs $^ > $@

ucotd: tools/ucotd.*
	cd tools; \
	$(PYTHON) ucotd.py

ucd.sqlite: ucotd tools/scripts.sql tools/scripts_wp.sql tools/fonts/*_insert.sql
	sqlite3 $@ <tools/scripts.sql
	sqlite3 $@ <tools/scripts_wp.sql
	sqlite3 $@ <tools/fonts/*_insert.sql

l10n: locale/messages.pot

locale/messages.pot: index.php lib/*.php controllers/*.php views/*.php \
                     views/*/*.php
	find index.php lib controllers views -name \*.php | \
		xargs xgettext -LPHP --from-code UTF-8 -k__ -k_e -k_n -kgettext -o $@

test: $(PHP_ALL)
	! find . -name \*.php -exec php -l '{}' \; | \
		grep -v '^No syntax errors detected in '
