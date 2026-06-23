FROM php:8.5-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    curl \
    git \
    zip \
    unzip \
    libpq \
    libzip \
    zlib \
    sqlite-libs \
    linux-headers \
    nodejs \
    npm

RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    build-base \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    zlib-dev \
    sqlite-dev \
    libpq-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    pdo_sqlite \
    mbstring \
    zip \
    bcmath \
    pcntl \
    sockets

# Install Redis extension via PECL
RUN pecl install redis && \
    docker-php-ext-enable redis && \
    apk del .build-deps

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy PHP configuration
COPY local.ini /usr/local/etc/php/conf.d/local.ini
COPY www.conf /usr/local/etc/php-fpm.d/www.conf

# Create app user
RUN addgroup -g 1000 app && \
    adduser -D -u 1000 -G app app

# Change ownership
RUN chown -R app:app /var/www/html

# Don't switch user - run as root so environment variables work
# USER app

CMD ["php-fpm"]
