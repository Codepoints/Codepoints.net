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
.PHONY: css

test: test-php
.PHONY: test

test-php: test-php-syntax test-php-psalm
.PHONY: test-php

test-php-syntax:
	@! find ./codepoints.net -path ./codepoints.net/vendor -prune -o \
		-name \*.php -exec php -l '{}' \; | \
		grep -v '^No syntax errors detected in '
.PHONY: test-php-syntax

test-php-psalm:
	@./codepoints.net/vendor/bin/psalm --show-info=true
.PHONY: test-php-psalm

vendor: $(DOCROOT)vendor/autoload.php

$(DOCROOT)vendor/autoload.php: composer.lock
	$(COMPOSER) install $(COMPOSER_ARGS)
	@touch $@

composer.lock: composer.json
	@touch $@
