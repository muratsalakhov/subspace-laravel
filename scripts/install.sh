#!/bin/bash

set -e

function echo_error {
    echo -e "\033[0;31m$1\033[0m"
}

function echo_success {
    echo -e "\033[0;32m$1\033[0m"
}

echo 'Собрать и поднять контейнеры в фоновом режиме...'
docker compose build
docker compose --profile workers up --detach

echo 'Установить зависимости...'
docker compose exec app composer install

echo 'Сгенерировать новый ключ приложения APP_KEY...'
docker compose exec app php artisan key:generate

echo 'Очистить кэш...'
docker compose exec app php artisan cache:clear

echo 'Создать символическую ссылку для файлов...'
docker compose exec app php artisan storage:link

echo 'Накатить миграции заново...'
docker compose exec app php artisan migrate:fresh

echo 'Сгенерировать документацию...'
docker compose exec app php artisan scribe:generate

echo_success 'Готово! Проверить состояние контейнеров можно с помощью `docker compose --profile workers ps`'
