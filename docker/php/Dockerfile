FROM php:8.1-fpm

RUN apt-get update && apt-get upgrade -y && apt-get install -y git

RUN apt-get install -y cron

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions intl pdo_pgsql bcmath

RUN apt-get clean

WORKDIR /app
