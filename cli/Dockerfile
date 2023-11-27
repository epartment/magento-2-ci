ARG PHP_VERSION
FROM php:$PHP_VERSION

ARG NODE_MAJOR

# Copy PhantomJS
COPY --from=rollupdev/phantomjs:latest /usr/local/bin/phantomjs /usr/local/bin/phantomjs

RUN set -eux; \
    apt-get update -y; \
    apt-get upgrade -y; \
    apt-get install -q -y libicu-dev libpng-dev libzip-dev rsync openssh-client zip unzip libmcrypt-dev libxml2-dev libxslt-dev git gnupg2; \
    export OPENSSL_CONF=/etc/ssl/ && apt-get install -y libfontconfig1 fontconfig libfontconfig1-dev libfontconfig; \
    curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -; \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list; \
    apt-get update -y; \
    apt-get install -q -y yarn; \
    mkdir -p /root/.composer;

# PHP Extension Installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions \
    gd \
    pdo_mysql \
    intl \
    bcmath \
    zip  \
    mysqli  \
    sockets  \
    soap  \
    xsl  \
    pcntl

RUN set -eux; \
    apt-get update; \
    apt-get install -y ca-certificates curl gnupg; \
    mkdir -p /etc/apt/keyrings; \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg; \
    if [ "$NODE_MAJOR" -lt "16" ]; then curl -sL https://deb.nodesource.com/setup_${NODE_MAJOR}.x | bash -; fi; \
    if [ "$NODE_MAJOR" -gt "15" &&  ]; then echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list; apt-get update; apt-get install nodejs -y; fi; \
    apt-get update; \
    apt-get install npm -y; \
    apt-get clean

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /root/.composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN export PATH=$PATH:/usr/bin

