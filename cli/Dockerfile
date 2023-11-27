ARG PHP_VERSION
FROM php:8.1

RUN set -eux; \
    apt-get update -y; \
    apt-get upgrade -y; \
    apt-get install -q -y libicu-dev libpng-dev libzip-dev rsync openssh-client zip unzip libmcrypt-dev libxml2-dev libxslt-dev git gnupg2 libfontconfig1 fontconfig libfontconfig1-dev libfontconfig; \
    curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -; \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list; \
    apt-get update -y; \
    apt-get install -q -y yarn; \
    mkdir -p /root/.composer;


RUN docker-php-ext-install gd pdo_mysql intl bcmath zip mysqli sockets soap xsl pcntl
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash - && apt-get install -y nodejs

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /root/.composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN export PATH=$PATH:/usr/bin

