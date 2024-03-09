FROM mkldevops/php-fpm-alpine:8.3

RUN apk add --no-cache \
    weasyprint \
    # used to find and configure fonts
    fontconfig \
    # used to render TrueType fonts
    freetype \
    # used as a default font
    ttf-dejavu \
    ;


COPY --link docker/app.ini $PHP_INI_DIR/conf.d/

EXPOSE 80
CMD ["symfony", "serve", "--no-tls", "--allow-http", "--port=80"]
