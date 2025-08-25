FROM php:8.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
  build-essential \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  zip \
  unzip \
  curl \
  git \
  libzip-dev \
  libjpeg-dev \
  libfreetype6-dev \
  bash \
  default-mysql-client \
  && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-scripts
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
EXPOSE 8000
ENTRYPOINT ["entrypoint.sh"]
