# Usar imagem oficial do PHP com Apache
FROM php:8.4-apache

# Instalar dependências do sistema e bibliotecas para extensões PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP necessárias para Laravel (MySQL e Postgres incluídos)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring zip gd bcmath


# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar arquivos do projeto para o container
COPY . /var/www/html

# Definir diretório de trabalho
WORKDIR /var/www/html

# Comando para instalar as dependencias
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Ajustar permissões para as pastas de storage e cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache


# Expor a porta 80
EXPOSE 80

# Usar o servidor built-in do PHP em vez do Apache
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]