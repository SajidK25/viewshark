FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    libzip-dev libonig-dev libicu-dev zlib1g-dev \
    unzip git curl ffmpeg && \
    rm -rf /var/lib/apt/lists/*

# PHP extensions needed by the app
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd exif zip intl sockets mysqli pdo_mysql

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# PHP runtime settings
RUN printf '%s\n' \
  'date.timezone=UTC' \
  'memory_limit=512M' \
  'upload_max_filesize=256M' \
  'post_max_size=256M' \
  'display_errors=On' \
  'error_reporting=E_ALL | E_DEPRECATED | E_STRICT' \
  'log_errors=On' \
  'error_log=/proc/self/fd/2' > /usr/local/etc/php/conf.d/zz-easystream.ini

# Configure PHP-FPM to pass environment variables
RUN printf '%s\n' \
  'clear_env = no' \
  'env[DB_HOST] = $DB_HOST' \
  'env[DB_NAME] = $DB_NAME' \
  'env[DB_USER] = $DB_USER' \
  'env[DB_PASS] = $DB_PASS' \
  'env[REDIS_HOST] = $REDIS_HOST' \
  'env[REDIS_PORT] = $REDIS_PORT' \
  'env[REDIS_DB] = $REDIS_DB' \
  'env[MAIN_URL] = $MAIN_URL' > /usr/local/etc/php-fpm.d/zz-environment.conf
