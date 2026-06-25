# ── Stage 1: Composer dependencies ──────────────────────────────────────────
FROM composer:2.7 AS composer-build
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-reqs
COPY . .
RUN composer dump-autoload \
    --optimize \
    --no-dev \
    --no-scripts
# ── Stage 2: Node / asset build ──────────────────────────────────────────────
FROM node:20-alpine AS node-build
WORKDIR /app
COPY package*.json ./
RUN npm ci --silent
COPY . .
RUN npm run production
# ── Stage 3: Production image ─────────────────────────────────────────────────
FROM php:8.2-apache
# System packages
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    unzip \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
        xml \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
# Apache config: point document root to /var/www/html/public + allow symlinks
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' \
        /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' \
        /etc/apache2/apache2.conf \
    && printf '\n<Directory /var/www/html/public>\n\tOptions Indexes FollowSymLinks MultiViews\n\tAllowOverride All\n\tRequire all granted\n</Directory>\n' \
        >> /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite headers
# PHP config
RUN echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size = 64M"    >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "memory_limit = 256M"   >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_execution_time = 120" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.enable=1"       >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/custom.ini
WORKDIR /var/www/html
# Copy application source
COPY . .
# Copy Composer vendor from stage 1
COPY --from=composer-build /app/vendor ./vendor
# Copy compiled frontend assets from stage 2
COPY --from=node-build /app/public ./public
# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache
# Copy Supervisor config (manages Apache + queue worker + scheduler)
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
# Copy entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
