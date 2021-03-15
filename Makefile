DOCROOT := codepoints.net/
DEBUG :=

PHP := php

COMPOSER := composer
COMPOSER_ARGS := --no-dev
ifeq ($(DEBUG), 1)
	COMPOSER_ARGS :=
endif

all: css
.PHONY: all

css:
	@mkdir -p "$(DOCROOT)/static/css/"
	@cp -u src/css/* "$(DOCROOT)/static/css/"
	@mkdir -p "$(DOCROOT)/static/fonts/"
	@cp -u src/fonts/* "$(DOCROOT)/static/fonts/"
.PHONY: css

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
