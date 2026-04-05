FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files first for caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy rest of app
COPY . .

RUN php artisan storage:link || true

EXPOSE 8000

CMD php artisan migrate --force && php artisan db:seed --force 2>/dev/null || true && php artisan serve --host=0.0.0.0 --port=8000
