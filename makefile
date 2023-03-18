clear.config:
	php artisan config:clear

docker.start:
	docker-compose up -d

docker.stop:
	docker-compose down

migrate.db:
	php artisan migrate

test.migrate.db:
	php artisan migrate --env=testing

test:
	php artisan test --env=testing  

test.migrate.reset:
	php artisan migrate:reset --env=testing        