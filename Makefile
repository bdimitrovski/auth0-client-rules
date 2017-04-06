build:
	@echo 'Building all containers...'
	@docker-compose build
	@echo 'Done'

up:
	@echo 'Starting all containers...'
	@docker-compose up -d
	@echo 'Done'

status:
	@docker-compose ps

restart:
	@echo 'Restarting all containers...'
	@docker-compose restart
	@echo 'Done'

stop:
	@echo 'Stopping all containers...'
	@docker-compose stop
	@echo 'Done'

down: stop

destroy:
	@echo 'Destroying all containers...'
	@docker-compose down
	@echo 'Done'

shell:
	@docker-compose exec php-fpm bash

install:
	@docker-compose exec -T php-fpm composer install