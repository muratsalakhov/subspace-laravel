.PHONY: up

up:
	docker-compose up -d --build

down:
	docker-compose down

scribe:
	docker-compose exec app php artisan scribe:generate

clear-cache:
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan route:clear
