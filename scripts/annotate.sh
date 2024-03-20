#!/bin/sh
docker-compose exec app php artisan ide-helper:generate
docker-compose exec app php artisan ide-helper:meta
docker-compose exec app php artisan ide-helper:models --write-mixin
