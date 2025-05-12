FROM php:7.4-fpm

# Giữ nguyên phần cài đặt dependencies hệ thống và PHP extensions
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libicu-dev \
    libxml2-dev \
    libxslt-dev \
    libzip-dev \
    git vim unzip cron \
    --no-install-recommends \
    && rm -r /var/lib/apt/lists/*

# Giữ nguyên phần cấu hình và cài đặt PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) intl
RUN docker-php-ext-install -j$(nproc) opcache bcmath pdo_mysql soap xsl zip sockets

# PHP-CUSTOM.INI - Giữ lại và đảm bảo file php-custom.ini tồn tại
# Đảm bảo file này nằm trong cùng thư mục docker/php7.4-fpm/
COPY ./php-custom.ini "$PHP_INI_DIR/conf.d/"

# INSTALL COMPOSER - Giữ lại
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php && \
    HASH=`curl -sS https://composer.github.io/installer.sig` && \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    rm /tmp/composer-setup.php

# Set working directory - Chuẩn hóa thành /var/www/html
WORKDIR /var/www/html

# --- PHẦN CẦN XÓA HOẶC COMMENT OUT ---
# # Copy Magento source code - SẼ THỰC HIỆN TRONG PIPELINE CI/CD
# COPY ../../src/magento/ /var/www/html/

# # Install Composer dependencies - SẼ THỰC HIỆN TRONG PIPELINE CI/CD
# # COPY ../../src/magento/composer.json /var/www/html/composer.json
# # COPY ../../src/magento/composer.lock /var/www/html/composer.lock
# # COPY ../../src/magento/auth.json.sample /var/www/html/auth.json # Cấu hình auth.json nếu cần cho private repo
# # RUN composer install --no-dev --optimize-autoloader --prefer-dist

# # Set permissions - SẼ THỰC HIỆN TRONG PIPELINE CI/CD (hoặc entrypoint)
# # RUN chown -R www-data:www-data /var/www/html
# # RUN find /var/www/html -type d -exec chmod 770 {} \; && \
# #     find /var/www/html -type f -exec chmod 660 {} \; && \
# #     chmod +x /var/www/html/bin/magento

# # Magento build commands - SẼ THỰC HIỆN TRONG PIPELINE CI/CD
# # RUN php bin/magento setup:upgrade && \
# #     php bin/magento setup:di:compile && \
# #     php bin/magento setup:static-content:deploy -f en_US vi_VN && \
# #     php bin/magento cache:flush && \
# #     php bin/magento indexer:reindex
# --- KẾT THÚC PHẦN XÓA/COMMENT ---

# Giữ nguyên Expose port và CMD
EXPOSE 9000
CMD ["php-fpm"]