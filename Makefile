.PHONY: composer docker check-exists cache-warmup

default: .env vendor cache
	@test -f docker-compose.yml || cp docker-compose.yml.dist docker-compose.yml

.env:
	@test -f .env || cp .env.dist .env

composer: composer.phar
	@php composer.phar install

distclean: clean
	@rm .env
	@rm docker-compose.yml

clean:
	@rm -rf .phpunit.cache 2>/dev/null || :
	@rm -rf cache 2>/dev/null || :
	@rm -rf config/initial-db 2>/dev/null || :
	@rm -rf composer.phar 2>/dev/null || :
	@rm -rf vendor 2>/dev/null || :
	@docker-compose down >/dev/null 2>&1 || :
	@docker-compose rm -s -f -v >/dev/null 2>&1 || :

test:
	@vendor/bin/phpunit

docker:
	@docker-compose up

cache: vendor cache/initial-db/mysql-sakila.downloaded cache-warmup

cache-warmup: vendor
	@php -r '(require "bootstrap.php")->get("CacheWarmup");'

vendor:
	@$(MAKE) --no-print-directory check-exists CMD=curl || exit 127
	@$(MAKE) --no-print-directory check-exists CMD=php || exit 127
	@$(MAKE) --no-print-directory check-exists CMD=docker || exit 127
	@$(MAKE) --no-print-directory check-exists CMD=docker-compose || exit 127
	@$(MAKE) --no-print-directory composer

check-exists:
	@echo -n "checking if "$$CMD" is installed..."
	@command -v "$$CMD" >/dev/null || { echo no && exit 127; }
	@echo yes

composer.phar:
	curl -# -o composer.phar https://getcomposer.org/download/latest-stable/composer.phar

cache/initial-db/mysql-sakila.downloaded: vendor
	@mkdir -p cache/initial-db
	curl -# -Lo "cache/initial-db/sakila-db.tar.gz" "https://downloads.mysql.com/docs/sakila-db.tar.gz" 
	
	@tar -xzf "cache/initial-db/sakila-db.tar.gz" --directory=cache/initial-db
	@mv cache/initial-db/sakila-db/* "cache/initial-db"
	@rm -rf cache/initial-db/sakila-db "cache/initial-db/sakila-db.tar.gz" "cache/initial-db/sakila.mwb"
	
	@mv "cache/initial-db/sakila-schema.sql" "cache/initial-db/01-sakila-schema.sql"
	@mv "cache/initial-db/sakila-data.sql" "cache/initial-db/02-sakila-data.sql"
	
	@echo "cache/initial-db downloaded at $$(date)" > "cache/initial-db/mysql-sakila.downloaded"