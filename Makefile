SHELL := /bin/bash
DOCROOT := codepoints.net/
DEBUG := 1

PHP := php

PHP_ALL := $(shell find $(DOCROOT) -type f -not -path \*/vendor/\* -name \*.php)
JS_ALL := $(shell find src/js -type f -name \*.\?s)

XGETTEXT := bin/xgettext

COMPOSER := composer
COMPOSER_ARGS := --no-dev
ifeq ($(DEBUG), 1)
	COMPOSER_ARGS :=
endif

all: css sw fonts views
.PHONY: all

css: vite-build
.PHONY: css

vite-build:
	@npm run build
.PHONY: vite-build

fonts: src/fonts/Literata.woff2 src/fonts/Literata-Italic.woff2
.PHONY: fonts

src/fonts/Literata.woff2 src/fonts/Literata-Italic.woff2: src/fonts/%.woff2: src/fonts/%.ttf
	pyftsubset "$<" \
		--layout-features='*' \
		--flavor=woff2 \
		--unicodes='U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD, U+2714, U+2718' \
		--output-file="$@"

sw: codepoints.net/sw.js
.PHONY: sw

codepoints.net/sw.js: workbox-config.js codepoints.net/static/*/*
	rm -f codepoints.net/workbox-*.js
	./node_modules/.bin/workbox generateSW workbox-config.js

views: codepoints.net/views/api.html
.PHONY: views

codepoints.net/views/api.html: openapi.yml
	./node_modules/.bin/redoc-cli bundle "$<"
	mv redoc-static.html "$@"

test: test-php test-js test-codeception
.PHONY: test

test-php: test-phpcs test-php-psalm
.PHONY: test-php

test-phpcs:
	@./codepoints.net/vendor/bin/phpcs
.PHONY: test-phpcs

test-php-psalm:
	@./codepoints.net/vendor/bin/psalm --show-info=true
.PHONY: test-php-psalm

test-js:
	@npx eslint src/js/
.PHONY: test-js

test-codeception:
	@./codepoints.net/vendor/bin/codecept run
.PHONY: test-codeception

vendor: $(DOCROOT)vendor/autoload.php

$(DOCROOT)vendor/autoload.php: composer.lock
	$(COMPOSER) install $(COMPOSER_ARGS)
	@touch $@

composer.lock: composer.json
	@touch $@

clear-cache:
	@./codepoints.net/vendor/bin/psalm --clear-cache
	@-rm -fr tests/_output/*
	@# TODO if we implement response caching, clear that here, too
.PHONY: clear-cache

shell:
	-@cd codepoints.net && php -d auto_prepend_file=init.php -a
.PHONY: shell

serve:
	-@php -S localhost:8000 -t codepoints.net bin/devrouter.php
.PHONY: serve

po: $(DOCROOT)locale/messages.pot $(DOCROOT)locale/js.pot
.PHONY: po

$(DOCROOT)locale/messages.pot: $(PHP_ALL)
	$(info * Compile PHP translation strings)
	@$(XGETTEXT) -LPHP --from-code UTF-8 -k__ -k_e -k_n -kgettext -o $@ $(PHP_ALL)

$(DOCROOT)locale/js.pot: $(JS_ALL)
	$(info * Compile JS translation strings)
	@$(XGETTEXT) --version | sed -n 1p | grep -qE ' 0\.([3-9][0-9]|2[1-9])' || { \
		echo 'xgettext v0.21 or higher needed!'; false; }
	@$(XGETTEXT) -LJavaScript --from-code UTF-8 -k__ -k_e -k_n -kgettext -o $@ $^

mo:
	$(info * Compile po to mo)
	@find codepoints.net/locale -type f -name '*.po' -print0 | \
		while IFS= read -r -d '' po; do \
			msgfmt "$$po" -o "$${po/.po/.mo}"; \
		done
.PHONY: mo
