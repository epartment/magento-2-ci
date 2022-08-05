ARG PHP_VERSION
FROM epartment/magento-2-ci:${PHP_VERSION}

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp/composer
COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

RUN export PATH=$PATH:/usr/bin

