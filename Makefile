test:
	php artisan test
test-coverage:
	php artisan test --coverage-clover build/logs/clover.xml
install:
	composer install
setup:
	install
	cp -n .env.example .env || true
	php artisan key:generate
	touch database/database.sqlite
	php artisan migrate
	seed
seed:
	php artisan db:seed
clear:
	php artisan route:clear
	php artisan view:clear
	php artisan cache:clear
	php artisan config:clear
