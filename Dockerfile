FROM php:8.1.9-alpine

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . /usr/src/myapp
WORKDIR /usr/src/myapp

RUN  composer install

EXPOSE 8080

ENTRYPOINT [ "php", "-S", "0.0.0.0:8080" ]