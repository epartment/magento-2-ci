FROM php:7.4-cli
RUN apt-get update && apt-get install -q -y libicu-dev libpng-dev libzip-dev
RUN docker-php-ext-install gd pdo_mysql intl bcmath zip mysqli sockets
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash - && apt-get install -y nodejs
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt-get update
RUN apt-get install zip unzip yarn libmcrypt-dev libxml2-dev libxslt-dev git -y
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar composer
RUN docker-php-ext-install soap xsl pcntl
RUN which ssh-agent || ( apt-get update -y && apt-get install openssh-client -y )
RUN export PATH=$PATH:/usr/bin
RUN which rsync || ( apt-get update -y && apt-get install rsync -y )
