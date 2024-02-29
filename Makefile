.PHONY: up

up:
	docker-compose up -d --build

down:
	docker-compose down