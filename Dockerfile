ARG PHP_VERSION
ARG OS_RELEASE

FROM ${ENV_SOURCE_IMAGE:-php}:${PHP_VERSION}-cli-${OS_RELEASE:-bullseye}
USER root
ARG PHP_VERSION

# Similar issue with other node commands : https://github.com/ariya/phantomjs/issues/15449
ENV OPENSSL_CONF=/etc/ssl/

# Install helpful utilities
RUN set -eux; \
    mkdir -p /root/.composer; \
    apt-get update -y; \
    export OPENSSL_CONF=/etc/ssl/; \
    apt-get install -y --no-install-recommends \
    libicu-dev \
    libpng-dev \
    libzip-dev \
    rsync \
    openssh-client \
    zip \
    unzip \
    libmcrypt-dev \
    libxml2-dev \
    libxslt-dev \
    git \
    gnupg2 \
    libfontconfig1 \
    fontconfig \
    libfontconfig1-dev \
    libfontconfig

# PHP Extension Installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install PHP Extensions required by Magento OS, Adobe Commerce, and the UCT (pcntl)
RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions \
    apcu \
    amqp \
    bcmath \
    calendar \
    exif \
    gd \
    intl \
    imagick \
    imap \
    mysqli \
    pcntl \
    pdo_mysql \
    redis \
    soap \
    sockets \
    sodium \
    xsl \
    zip

# If PHP < 8.2 install mcrypt
RUN if [ "$(printf "8.2\n${PHP_VERSION}" | sort -g | head -n1)" != "8.2" ]; \
    then install-php-extensions mcrypt; fi

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /root/.composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# clean up apt packages and temporary files
RUN set -eux; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists

RUN export PATH=$PATH:/usr/bin

