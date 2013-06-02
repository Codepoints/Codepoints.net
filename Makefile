
SHELL := /bin/bash
DOCROOT := codepoints.net/
JS_ALL := $(shell find src/js -type f -name \*.js)
JS_ROOTS := $(wildcard src/js/*.js)
JS_TARGET := $(patsubst src/js/%,$(DOCROOT)static/js/%,$(JS_ROOTS))
PHP_ALL := $(shell find $(DOCROOT) -type f -name \*.php)
SASS_ROOTS := $(wildcard src/sass/[^_]*.scss)
CSS_TARGET := $(patsubst src/sass/%.scss,$(DOCROOT)static/css/%.css,$(SASS_ROOTS))
PYTHON := python
SAXON := saxonb-xslt

all: test ucotd css js cachebust

.PHONY: all css js dist clean ucotd cachebust l10n test vendor db clearcache \
        test-sass

clean:
	-rm -fr dist src/vendor node_modules .sass-cache

dist: vendor all
	mkdir $@
	cp -r $(DOCROOT) $@
	sed -i 's/define(.CP_DEBUG., .);/define('"'CP_DEBUG'"', 0);/' $@/$(DOCROOT)index.php

css: $(CSS_TARGET)

$(CSS_TARGET): $(DOCROOT)static/css/%.css : src/sass/%.scss
	compass compile --force $<

js: $(DOCROOT)static/js/build.txt $(DOCROOT)static/js/html5shiv.js

$(DOCROOT)static/js/build.txt: src/build.js $(JS_ALL)
	cd src && node vendor/r.js/dist/r.js -o build.js
	-rm -fr $(DOCROOT)static/js/components $(DOCROOT)static/js/polyfills

$(DOCROOT)static/js/html5shiv.js: src/vendor/html5shiv/dist/html5shiv.js
	<$< node_modules/uglify-js/bin/uglifyjs >$@

cachebust: $(JS_ALL) $(CSS_TARGET)
	$(info * Update Cache Bust Constant)
	@sed -i '/^define(.CACHE_BUST., .\+.);$$/s/.*/define('"'CACHE_BUST', '"$$(cat $^ | sha1sum | awk '{ print $$1 }')"');/" $(DOCROOT)index.php

db: $(DOCROOT)ucd.sqlite

ucotd: tools/ucotd.*
	$(info * Add Codepoint of the Day)
	@cd tools; \
	$(PYTHON) ucotd.py

$(DOCROOT)ucd.sqlite: ucotd tools/scripts.sql tools/scripts_wp.sql \
            tools/fonts/*_insert.sql tools/latex.sql
	sqlite3 $@ <tools/scripts.sql
	sqlite3 $@ <tools/scripts_wp.sql
	sqlite3 $@ <tools/fonts/*_insert.sql
	sqlite3 $@ <tools/latex.sql

l10n: $(DOCROOT)locale/messages.pot $(DOCROOT)locale/js.pot

l10n-finish:
	tools/my-po2json.js de

locale/messages.pot: $(PHP_ALL)
	$(info * Compile translation strings)
	xargs xgettext -LPHP --from-code UTF-8 -k__ -k_e -k_n -kgettext -o $@ $(PHP_ALL)

locale/js.pot: $(JS_ALL)
	#node_modules/jsxgettext/lib/cli.js -k _ -o $@ $^
	xgettext -LPerl --from-code UTF-8 -k_ -o - $^ | \
		sed '/^#, perl-format$$/d' > $@

vendor: bower.json
	npm install
	node_modules/bower/bin/bower install
	$(MAKE) -C src/vendor/d3 d3.v2.js NODE_PATH=../../../node_modules
	node_modules/jqueryui-amd/jqueryui-amd.js src/vendor/jquery.ui
	cd src/vendor/webfontloader && rake compile

test: test-php test-sass test-js

test-php: $(PHP_ALL)
	$(info * Test PHP syntax)
	@! find $(DOCROOT) -name \*.php -exec php -l '{}' \; | \
		grep -v '^No syntax errors detected in '

test-js: $(JS_ALL)
	$(info * Test JS syntax)
	@node_modules/jshint/bin/jshint $^

test-sass: $(shell find src/sass -type f)
	$(info * Test Sass syntax)
	@sass --check $^

clearcache:
	rm -f $(DOCROOT)cache/_cache_* $(DOCROOT)cache/blog-preview*

tools/latex.sql: tools/latex.xsl tools/latex.xml
	$(SAXON) -xsl:tools/latex.xsl -s:tools/latex.xml -o:$@

tools/latex.xml:
	wget -O tools/latex.xml http://www.w3.org/Math/characters/unicode.xml
	wget -O tools/charlist.dtd http://www.w3.org/Math/characters/charlist.dtd
