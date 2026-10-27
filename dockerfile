FROM php:8.5-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    # Fornece bibliotecas necessárias para compilar o suporte ao PostgreSQL.
        libpq-dev \
    # Auxiliam o Composer a baixar e extrair dependências.
        git \
        unzip \
    # Permite conectar PHP ao PostgreSQL usando PDO.
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/list/*
# Copia o executável do Composer da imagem oficial.
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Define onde os comandos serão executados no container.
WORKDIR /var/www/html

# Configura o site do apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80