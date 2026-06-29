FROM mcr.microsoft.com/devcontainers/php:8.4

WORKDIR /var/www/html

RUN docker-php-ext-install ftp
RUN docker-php-ext-enable ftp

RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.5/install.sh | bash
RUN \. "/usr/local/share/nvm/nvm.sh" && nvm install 26 && nvm use 26

# DocumentRoot を php_code に変更
ENV APACHE_DOCUMENT_ROOT=/var/www/html/php/src
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf