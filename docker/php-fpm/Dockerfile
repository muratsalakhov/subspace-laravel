FROM php:8.3-fpm

ARG HOST_USER_ID
ARG HOST_GROUP_ID

RUN if [ ${HOST_USER_ID:+1} ]; then useradd -u $HOST_USER_ID -ms /bin/bash hostuser; fi

# Установите необходимые зависимости
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Установка Composer
COPY --from=composer:2.7.1 /usr/bin/composer /usr/bin/composer

# Установка переменных среды для Composer
ENV COMPOSER_CACHE_DIR=/var/www/html/.composer/cache \
    COMPOSER_HOME=/var/www/html/.composer

# Копирование приложения
COPY . /var/www/html

# Установка рабочей директории
WORKDIR /var/www/html

# Установка прав на директории для кэша и конфигураций Composer
RUN mkdir -p /var/www/html/.composer/cache /var/www/html/.composer && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html

# Генерация ключа приложения
RUN php artisan key:generate

# Открытие порта 9000
EXPOSE 9000

CMD ["php-fpm"]

USER hostuser
