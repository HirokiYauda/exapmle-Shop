FROM php:7.4-fpm
# install composer
RUN cd /usr/bin && curl -s http://getcomposer.org/installer | php && ln -s /usr/bin/composer.phar /usr/bin/composer
RUN apt-get update \
&& apt-get install -y \
git \
zip \
unzip \
vim \
libmemcached-dev \
libzip-dev \
&& pecl install memcached \
&& docker-php-ext-enable memcached
RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql
WORKDIR /var/www/html

# shellをbashに変更
SHELL ["/bin/bash", "-c"]
# nvmをインストール
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.37.2/install.sh | bash