FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    libzip-dev libonig-dev libicu-dev zlib1g-dev \
    unzip git curl ffmpeg && \
    rm -rf /var/lib/apt/lists/*

# PHP extensions needed by the app
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd exif zip intl sockets mysqli pdo_mysql

# PHP runtime settings
RUN printf '%s\n' \
  'date.timezone=UTC' \
  'memory_limit=512M' \
  'upload_max_filesize=256M' \
  'post_max_size=256M' > /usr/local/etc/php/conf.d/zz-viewshark.ini
