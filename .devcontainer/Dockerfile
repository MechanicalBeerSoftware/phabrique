FROM ubuntu:24.04

WORKDIR /install

RUN apt update

# Install tooling
RUN apt install -y git wget curl zip unzip

# Install PHP
RUN apt install -y php8.3 php8.3-cli

# Install PHP extensions
RUN apt install -y php8.3-xml php8.3-fpm php8.3-xdebug php8.3-curl php8.3-zip php8.3-mbstring

# Install Composer
RUN wget 'https://getcomposer.org/installer'
RUN php installer --version=2.7.9 --install-dir=/usr/local/bin --filename=composer
RUN rm installer

WORKDIR /workspace