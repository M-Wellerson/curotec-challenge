# Build stage
FROM node:18-alpine AS build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Production stage
FROM php:8.3-fpm-alpine
WORKDIR /var/www/html

# Install dependencies
RUN apk add --no-cache bash git zip libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Copy application files
COPY . .
COPY --from=build /app/public/build public/build

# Install PHP dependencies
RUN composer install --prefer-dist --no-dev --optimize-autoloader \
    && rm -rf /var/cache/apk/* /root/.composer

CMD ["php-fpm"]
