FROM php:8.1-cli-alpine

ARG UID=1000
ARG GID=1000

WORKDIR /var/www/html
RUN apk add --no-cache shadow
RUN groupmod -g $GID www-data
RUN usermod -u $UID www-data


#-#-#-#-#-#-#-#-#-#-#-#-#-#-#
#        Install composer
#-#-#-#-#-#-#-#-#-#-#-#-#-#-#
COPY --chown=www-data:www-data --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY --chown=www-data:www-data ./src ./
USER www-data
