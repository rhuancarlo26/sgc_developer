# Etapa 1: Instalar dependências PHP e configurar o ambiente base
FROM php:8.2-apache

# Instalar extensões PHP
RUN docker-php-ext-install pdo pdo_mysql

# Copiar arquivo de configuração do PHP
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Habilitar módulo rewrite do Apache
RUN a2enmod rewrite

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    nano \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libmagickwand-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install zip \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && rm -rf /var/lib/apt/lists/*

# Copiar configuração do Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar Node.js e npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Etapa 2: Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar os arquivos do projeto
COPY . /var/www/html

# Instalar dependências do front-end (caso necessário)
RUN npm install