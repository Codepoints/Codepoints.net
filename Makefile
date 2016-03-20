
SHELL := /bin/bash
DOCROOT := codepoints.net/

DEPLOY :=

JS_ALL := $(shell find src/js -type f -name \*.js)
JS_SOURCES := $(wildcard src/js/*.js)
JS_TARGETS := $(patsubst src/js/%,$(DOCROOT)static/js/%,$(JS_SOURCES))

# no colon, re-evaluate variable on building cache buster
STATICS_ALL = $(shell find $(DOCROOT)static -type f)

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

JSPM := node_modules/.bin/jspm
JSPM_BUILD_ARGS := --format global \
    --global-deps "{'jquery': 'jQuery'}" \
    --minify --skip-source-maps

UGLIFY := node_modules/.bin/uglifyjs
UGLIFY_ARGS := --compress --mangle

ifdef COVERAGE
PHPUNIT_REAL_ARGS := $(PHPUNIT_ARGS) --coverage-html ./coverage-report
else
PHPUNIT_REAL_ARGS := $(PHPUNIT_ARGS)
endif

COMPOSER_ARGS :=
ifeq ($(DEPLOY), true)
COMPOSER_ARGS := --no-dev
endif


all: vendor test css js cachebust

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
	$(info * build $@)
	@if [[ ! -f $(SASS) ]]; then $(MAKE) vendor; fi
	@<"$<" $(SASS) $(SASS_ARGS) | $(POSTCSS) $(POSTCSS_ARGS) >"$@"


js: $(JS_TARGETS) $(DOCROOT)static/js/html5shiv.js

$(JS_TARGETS): $(DOCROOT)static/js/%.js: src/js/%.js src/js/*/*.js
	$(info * build $@)
	@if [[ ! -f $(JSPM) ]]; then $(MAKE) vendor; fi
	@if [[ $$(basename $@) == 'main.js' ]]; then \
		$(JSPM) build $< $@ \
			--global-name $$(basename $@ .js) \
			$(JSPM_BUILD_ARGS) ; \
	else \
		$(JSPM) build $< $@ \
			--global-name $$(basename $@ .js) \
			$(JSPM_BUILD_ARGS) ; \
	fi


$(DOCROOT)static/js/html5shiv.js: node_modules/html5shiv/dist/html5shiv.js
	<$< $(UGLIFY) $(UGLIFY_ARGS) >$@

node_modules/html5shiv/dist/html5shiv.js: vendor


cachebust: $(JS_ALL) $(CSS_TARGET) $(DOCROOT)lib/cachebust.php

$(DOCROOT)lib/cachebust.php: $(STATICS_ALL)
	$(info * pre-calculate hashes for statc files)
	@( \
		echo '<?php $$cachebust = ['; \
		echo '$^' | \
			xargs md5sum | \
			awk '{ print $$2 " " $$1 }' | \
			sed 's/codepoints.net\/\(.\+\) \(.\+\)/"\1"=>"\2",/'; \
		echo '];'; \
	) > $(DOCROOT)lib/cachebust.php


db: db.conf

db.conf:
	# To get the database up and running, create a file
	# `db.conf` in the folder of this Makefile with this content:
	#
	# [clientreadonly]
	# password=mysql-password
	# user=mysql-user
	# database=mysql-database
	#
	# Then download https://dumps.codepoints.net/latest.sql.gz and
	# feed it into the above database.

ucotd: tools/ucotd.*
	@echo "* Add Codepoint of the Day"
	@cd tools; \
	$(PYTHON) ucotd.py

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
	@node_modules/jsxgettext/lib/cli.js -k _ -o $@ $^

vendor: $(DOCROOT)lib/vendor/autoload.php jspm_packages/system.js

$(DOCROOT)lib/vendor/autoload.php: composer.lock
	@mkdir -p $(DOCROOT)lib/vendor
	composer install $(COMPOSER_ARGS)
	@touch $@

composer.lock: composer.json
	@touch $@

jspm_packages/system.js: node_modules/jspm/README.md
	$(JSPM) install
	@touch $@

node_modules/jspm/README.md: package.json
	npm install
	$(info * patch jsxgettext)
	@-patch node_modules/jsxgettext/lib/jsxgettext.js 80_jsxgettext.diff \
		--forward --quiet --reject-file=-
	@touch $@

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
	$(info Buhu! They do not run cleanly, yet. #FIXME)
	@#cd test/casperjs; $(CASPERJS) test --pre=bootstrap.js $(CASPERJS_ARGS) test_*.js

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
.PHONY: search_index
