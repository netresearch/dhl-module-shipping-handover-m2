#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && exit 0

set -xe

apt-get update && apt-get install -y wget \
        libcurl4-gnutls-dev \
        libfreetype6-dev \
        libicu-dev \
        rst2pdf \
        libmcrypt-dev \
        libxml2-dev \
        libxslt1-dev \
        git \
        zip \
        unzip \
    && docker-php-ext-install -j$(nproc) curl dom hash iconv intl json mbstring mcrypt simplexml xml xsl soap zip

# install magento-tools
wget https://raw.githubusercontent.com/magento/marketplace-tools/master/validate_m2_package.php -O /usr/local/bin/m2eval && \
    chmod +x /usr/local/bin/m2eval
