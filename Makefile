DOCROOT := codepoints.net/
DEBUG := 1

PHP := php

COMPOSER := composer
COMPOSER_ARGS := --no-dev
ifeq ($(DEBUG), 1)
	COMPOSER_ARGS :=
endif

all: css sw fonts views
.PHONY: all

css:
	@if [ "$(DEBUG)" ]; then \
		echo "in debug mode, symlink src/css to codepoints.net/static/css"; \
	else \
		mkdir -p "$(DOCROOT)/static/css/" ; \
		cp -u src/css/* "$(DOCROOT)/static/css/" ; \
		mkdir -p "$(DOCROOT)/static/fonts/" ; \
		cp -u src/fonts/* "$(DOCROOT)/static/fonts/" ; \
	fi
.PHONY: css

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

test: test-php test-codeception
.PHONY: test

test-php: test-phpcs test-php-psalm
.PHONY: test-php

test-phpcs:
	@./codepoints.net/vendor/bin/phpcs
.PHONY: test-php-syntax

test-php-psalm:
	@./codepoints.net/vendor/bin/psalm --show-info=true
.PHONY: test-php-psalm

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
