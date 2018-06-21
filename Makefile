SHELL=/bin/bash
.PHONY: help install update ssh check fix test build

help: ## Show this help
	@echo "Targets:"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/\(.*\):.*##[ \t]*/    \1 ## /' | sort | column -t -s '##'

install: ## Build the image
	docker-compose build && \
	docker-compose run docker-php sh -c "composer install"

update: ## Update dependencies with composer
	docker-compose run docker-php sh -c "composer update"

ssh: ## Start a shell in the container
	docker-compose run docker-php sh

check: ## Check the source files with code sniffer
	docker-compose run docker-php sh -c "./vendor/bin/phpcs --colors"

fix: ## Fix the problems found by code sniffer
	docker-compose run docker-php sh -c "./vendor/bin/phpcbf"

test: ## Run tests
	docker-compose run docker-php sh -c "./vendor/bin/phpunit --colors=always"

build: ## Build the phar file from source code
	./vendor/bin/box build && chmod u+x bin/ecc.phar
	gpg -u negabor@gmail.com --detach-sign --output ./bin/ecc.phar.asc ./bin/ecc.phar
