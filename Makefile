.PHONY: install test quality cs-fix phpstan psalm

install:
	composer install
	cp .env.example .env
	php artisan key:generate
	chmod -R 755 storage bootstrap/cache

test:
	php artisan test

quality: phpstan psalm cs-fix

phpstan:
	./vendor/bin/phpstan analyse

psalm:
	./vendor/bin/psalm

cs-fix:
	./vendor/bin/pint

dev-setup: install
	mkdir -p storage/app/private

docker-setup:
	docker compose up -d --build
	docker compose exec app composer install
	docker compose exec app php artisan key:generate
