FROM phpstorm/php-apache:8.2-xdebug3.2
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN apt-get update && apt-get -y install libzip-dev libicu-dev

RUN docker-php-ext-install pdo pdo_mysql zip mysqli
RUN docker-php-ext-enable pdo pdo_mysql zip mysqli

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

COPY . /var/www/html
COPY my-httpd.conf /etc/apache2/sites-available/
RUN a2enmod rewrite

WORKDIR /var/www/html


CMD ["sh", "-c", "composer install || true && composer update || true && php bin/console make:migration |ue && php bin/console doctrine:migrations:migrate || true && php bin/console messenger:consume async & apache2-foreground"]

