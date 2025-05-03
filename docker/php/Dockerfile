FROM php:7.4.9-fpm

COPY php.ini /usr/local/etc/php/

RUN apt update \
    && apt install -y default-mysql-client zlib1g-dev libzip-dev unzip \
    && docker-php-ext-install pdo_mysql zip

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer self-update


# `www-data` の UID をホストPCの `sugita` と合わせる
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# `/var/www` の所有者を変更（重要！）
RUN chown -R www-data:www-data /var/www

# 作業ディレクトリを `/var/www` に設定
WORKDIR /var/www

# www-data ユーザーでコンテナを実行
USER www-data
