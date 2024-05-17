FROM mkldevops/php-fpm-alpine:latest as base

RUN apk add --no-cache \
    weasyprint \
    # used to find and configure fonts
    fontconfig \
    # used to render TrueType fonts
    freetype \
    # used as a default font
    ttf-dejavu \
    ;

ENV APP_ENV=prod

COPY --link docker/app.ini $PHP_INI_DIR/conf.d/

EXPOSE 80
CMD ["symfony", "serve", "--no-tls", "--allow-http", "--port=80"]

FROM base as prod

COPY --link . .
RUN set -eux; \
	symfony composer install --no-cache --prefer-dist --no-scripts --no-progress

FROM base as dev

ENV APP_ENV=dev
