# Compilação do CSS para a imagem de produção.
FROM node:26-alpine AS assets
WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci --include=dev
COPY resources/ ./resources/
RUN npm run build

# Base PHP compartilhada pelos dois ambientes.
FROM php:8.5-apache AS php-base
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    # Fornece bibliotecas necessárias para compilar o suporte ao PostgreSQL.
        libpq-dev \
    # Auxiliam o Composer a baixar e extrair dependências.
        git \
        unzip \
    # Permite conectar PHP ao PostgreSQL usando PDO.
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*
# Copia o executável do Composer da imagem oficial.
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Define onde os comandos serão executados no container.
WORKDIR /var/www/html

# Configura o site do apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

FROM php-base AS development

# Produção: código, dependências PHP e CSS dentro da imagem.
FROM php-base AS production

COPY composer.json composer.lock ./
COPY app/ ./app/
COPY routes/ ./routes/
COPY resources/ ./resources/
COPY public/ ./public/

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader

COPY --from=assets /build/public/assets/css/app.css \
    /var/www/html/public/assets/css/app.css