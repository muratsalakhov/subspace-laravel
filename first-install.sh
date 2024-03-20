
# Останавливать скрипт при ошибке
set -e

function echo_error {
    echo -e "\033[0;31m$1\033[0m"
}

function echo_success {
    echo -e "\033[0;32m$1\033[0m"
}

# Проверить существование файла .env и скопировать .env.example в .env если необходимо
if [ ! -f .env ]; then
    echo 'Копирование .env.example в .env...'
    cp .env.example .env
else
    echo '.env файл уже существует.'
fi

./scripts/install.sh
