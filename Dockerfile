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

RUN chsh -s $(which zsh)

EXPOSE 80
CMD ["symfony", "serve", "--no-tls", "--allow-http", "--port=80"]