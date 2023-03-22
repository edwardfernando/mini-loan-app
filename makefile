run:
	php artisan serve

clear.config:
	php artisan config:clear

docker.start:
	docker-compose up -d

docker.stop:
	docker-compose down

migrate.db:
	php artisan migrate

migrate.reset:
	php artisan migrate:reset

test.migrate.db:
	php artisan migrate --env=testing

test:
	php artisan test --env=testing  

test.migrate.reset:
	php artisan migrate:reset --env=testing        