up:
	./vendor/bin/sail up -d
	./vendor/bin/sail exec laravel.test php artisan migrate
down:
	./vendor/bin/sail down
restart:
	./vendor/bin/sail down
	./vendor/bin/sail up -d
migrate:
	./vendor/bin/sail artisan migrate
fresh:
	./vendor/bin/sail artisan migrate:fresh --seed
bash:
	./vendor/bin/sail exec laravel.test /bin/bash
test:
	./vendor/bin/sail exec laravel.test php artisan test
pint:
	./vendor/bin/sail exec laravel.test ./vendor/bin/pint
