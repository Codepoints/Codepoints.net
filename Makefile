
SHELL := /bin/bash
DOCROOT := codepoints.net/

DEPLOY :=

JS_ALL := $(shell find src/js -type f -name \*.js)
JS_ROOTS := $(wildcard src/js/*.js)
JS_TARGET := $(patsubst src/js/%,$(DOCROOT)static/js/%,$(JS_ROOTS))

PHP_ALL := $(shell find $(DOCROOT) -type f -not -path \*/lib/vendor/\* -name \*.php)

SASS_ROOTS := $(wildcard src/sass/[^_]*.scss)
CSS_TARGET := $(patsubst src/sass/%.scss,$(DOCROOT)static/css/%.css,$(SASS_ROOTS))

PYTHON := python
SAXON := saxonb-xslt

JSHINT := node_modules/.bin/jshint
JSHINT_ARGS := --config src/jshint.js

SASS := node_modules/.bin/node-sass
SASS_ARGS := --quiet --include-path=src/sass

POSTCSS := node_modules/.bin/postcss
POSTCSS_ARGS := --use autoprefixer --use postcss-import --use cssnano

PHPUNIT := $(DOCROOT)lib/vendor/bin/phpunit
PHPUNIT_ARGS :=

PHPCS := $(DOCROOT)lib/vendor/bin/phpcs
PHPCS_ARGS :=

CASPERJS := casperjs
CASPERJS_ARGS := --fail-fast

ifdef COVERAGE
PHPUNIT_REAL_ARGS := $(PHPUNIT_ARGS) --coverage-html ./coverage-report
else
PHPUNIT_REAL_ARGS := $(PHPUNIT_ARGS)
endif

COMPOSER_ARGS :=
ifeq ($(DEPLOY), true)
COMPOSER_ARGS := --no-dev
endif


all: vendor test ucotd css js cachebust

.PHONY: all css js dist clean ucotd cachebust l10n test vendor db clearcache \
        test-php test-phpunit test-js init

clean:
	-rm -fr dist node_modules .sass-cache
	-rm -f tools/encoding-aliases.sql

#deploy: vendor css js cachebust
deploy: vendor cachebust
	sed -i 's/define(.CP_DEBUG., .);/define('"'CP_DEBUG'"', 0);/' $(DOCROOT)index.php

css: $(CSS_TARGET)

$(CSS_TARGET): $(DOCROOT)static/css/%.css : src/sass/%.scss
	<"$<" $(SASS) $(SASS_ARGS) | $(POSTCSS) $(POSTCSS_ARGS) >"$@"

js: node_modules/jquery-ui \
    $(DOCROOT)static/js/build.txt $(DOCROOT)static/js/html5shiv.js \
    $(DOCROOT)static/ZeroClipboard.swf

node_modules/jquery-ui:
	node_modules/.bin/jqueryui-amd "$@"

init: node_modules/jquery-ui/jqueryui node_modules/webfontloader/target/webfont.js

node_modules/jquery-ui/jqueryui:
	node_modules/.bin/jqueryui-amd "$@"

node_modules/webfontloader/target/webfont.js:
	cd node_modules/webfontloader && \
		rake compile

$(DOCROOT)static/js/build.txt: src/build.js $(JS_ALL)
	cd src && ../node_modules/.bin/r.js -o build.js

$(DOCROOT)static/js/html5shiv.js: node_modules/html5shiv/dist/html5shiv.js
	<$< node_modules/.bin/uglifyjs -c -m >$@

$(DOCROOT)static/ZeroClipboard.swf: node_modules/zeroclipboard/ZeroClipboard.swf
	cp "$<" "$@"

cachebust: $(JS_ALL) $(CSS_TARGET)
	$(info * Update Cache Bust Constant)
	@sed -i '/^define(.CACHE_BUST., .\+.);$$/s/.*/define('"'CACHE_BUST', '"$$(cat $^ | sha1sum | awk '{ print $$1 }')"');/" $(DOCROOT)index.php

db: ucd.sqlite

ucotd: tools/ucotd.*
	@echo "* Add Codepoint of the Day"
	@cd tools; \
	$(PYTHON) ucotd.py

ucd.sqlite: ucotd tools/scripts.sql tools/scripts_wp.sql \
            tools/latex.sql tools/fonts/fonts.sql \
            tools/fonts/target/sql/*.sql \
            tools/encoding-aliases.sql
	@echo "* Add additional info to DB"
	@sqlite3 $@ <tools/aliases.sql
	@sqlite3 $@ <tools/scripts.sql
	@sqlite3 $@ <tools/scripts_wp.sql
	@sqlite3 $@ <tools/latex.sql
	@echo "* Add font info"
	@sqlite3 $@ <tools/fonts/fonts.sql
	@for SQL in $$(find tools/fonts/target/sql -type f); do \
		echo "  + Processing $$SQL"; \
		python tools/insert.py $$SQL $@; \
	done
	@sqlite3 $@ <tools/encoding-aliases.sql
	@echo "* Create search index"
	@$(MAKE) search_index

tools/encoding-aliases.sql: tools/encoding tools/encoding/index-*.txt
	-true > $@
	cd tools && \
	for enc in encoding/index-*.txt; do \
		encoding-aliases.py $$enc >> encoding-aliases.sql; \
	done

tools/encoding:
	test -d tools/encoding || git clone git@github.com:whatwg/encoding.git tools/encoding
	cd tools/encoding && git pull

l10n: $(DOCROOT)locale/messages.pot $(DOCROOT)locale/js.pot

l10n-finish:
	node tools/my-po2json.js de

$(DOCROOT)locale/messages.pot: $(PHP_ALL)
	$(info * Compile PHP translation strings)
	@xgettext -LPHP --from-code UTF-8 -k__ -k_e -k_n -kgettext -o $@ $(PHP_ALL)

$(DOCROOT)locale/js.pot: $(JS_ALL)
	$(info * Compile JS translation strings)
	#node_modules/jsxgettext/lib/cli.js -k _ -o $@ $^
	@xgettext -LPerl --from-code UTF-8 -k_ -o - $^ | \
		sed '/^#, perl-format$$/d' > $@

vendor: $(DOCROOT)lib/vendor/autoload.php
	npm install
	#$(MAKE) -C node_modules/d3 d3.v2.js NODE_PATH=../../../node_modules
	#node_modules/jqueryui-amd/jqueryui-amd.js node_modules/jquery-ui
	#cd node_modules/webfontloader && rake compile

$(DOCROOT)lib/vendor/autoload.php: composer.lock
	@mkdir -p $(DOCROOT)lib/vendor
	composer install $(COMPOSER_ARGS)
	touch $@

composer.lock: composer.json
	touch $@

test: test-php test-phpunit test-js test-casper

test-phpunit:
	$(info * Run PHPUnit tests)
	@$(PHPUNIT) $(PHPUNIT_REAL_ARGS)

test-php: $(PHP_ALL)
	$(info * Test PHP syntax)
	@! echo $^ | xargs -n 1 php -l | \
		grep -v '^No syntax errors detected in '
	$(info * Call PHP CodeSniffer)
	@$(PHPCS) $(PHPCS_ARGS)

test-js: $(JS_ALL)
	$(info * Test JS syntax)
	@$(JSHINT) $(JSHINT_ARGS) $^

test-casper:
	$(info * run CasperJS tests)
	@cd test/casperjs; $(CASPERJS) test --pre=bootstrap.js $(CASPERJS_ARGS) test_*.js

clearcache:
	rm -f $(DOCROOT)cache/_cache_* $(DOCROOT)cache/blog-preview*

tools/scripts_wp.sql: tools/scripts_wp.py
	cd tools && python scripts_wp.py

tools/latex.sql: tools/latex.xsl tools/latex.xml
	$(SAXON) -xsl:tools/latex.xsl -s:tools/latex.xml -o:$@

tools/latex.xml:
	wget -O tools/latex.xml http://www.w3.org/Math/characters/unicode.xml
	wget -O tools/charlist.dtd http://www.w3.org/Math/characters/charlist.dtd

search_index:
	cd tools && python create_search_index.py --print > search_index.sql
	cd tools && python insert.py search_index.sql ../ucd.sqlite
.PHONY: search_index
