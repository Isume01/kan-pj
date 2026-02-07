FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    default-mysql-client \
    git \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP拡張のインストール
RUN docker-php-ext-install pdo_mysql intl zip bcmath opcache

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ソースコードのコピー
COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80