FROM alpine:3.24

RUN apk update && apk upgrade && apk add --no-cache \
    curl

RUN echo "https://pkg.henderkes.com/api/packages/84/alpine/main/php-zts" | tee -a /etc/apk/repositories && \
    KEYFILE=$(curl -sJOw '%{filename_effective}' https://pkg.henderkes.com/api/packages/84/alpine/key) && \
    mv ${KEYFILE} /etc/apk/keys/

RUN apk add --no-cache \
    libgcc \
    php84 \
    frankenphp

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer
