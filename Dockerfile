# Dockerfile.app - Để build image ứng dụng Magento cuối cùng

# Sử dụng image PHP-FPM runtime đã chuẩn bị làm base
# Thay 'yourusername/techworld-php-fpm:latest' bằng tên image base của bạn
ARG BASE_IMAGE=pknguyen2704/techworld-php-fpm:latest
FROM ${BASE_IMAGE}

# Argument để nhận các biến môi trường build-time nếu cần
# ARG COMPOSER_AUTH_TOKEN

# Copy composer files trước để tận dụng Docker cache
COPY src/magento/composer.json src/magento/composer.lock* /var/www/magento/
# Copy auth.json nếu bạn sử dụng private repository và đã tạo file này
# COPY src/magento/auth.json /var/www/magento/auth.json

# Đảm bảo WORKDIR đúng (đã set trong base image, nhưng để đây cho rõ)
WORKDIR /var/www/magento

# Cài đặt Composer dependencies
# Chạy với --no-interaction để không hỏi đáp trong quá trình build
# Sử dụng biến môi trường Composer nếu cần (ví dụ: COMPOSER_AUTH_TOKEN)
# RUN if [ -n "$COMPOSER_AUTH_TOKEN" ]; then composer config -g github-oauth.github.com $COMPOSER_AUTH_TOKEN; fi && \
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
# RUN if [ -n "$COMPOSER_AUTH_TOKEN" ]; then composer config -g --unset github-oauth.github.com; fi

# Copy toàn bộ mã nguồn ứng dụng vào WORKDIR
# Lưu ý: Nên có file .dockerignore ở thư mục gốc để loại bỏ các file/thư mục không cần thiết (node_modules, .git, data/, etc.)
COPY src/magento/ /var/www/magento/

# (Tùy chọn nhưng khuyến nghị) Điều chỉnh memory limit cho PHP CLI nếu các lệnh build cần nhiều RAM
# RUN echo 'memory_limit = 2G' >> $PHP_INI_DIR/conf.d/zz-magento-cli.ini

# Chạy các lệnh build Magento
# Thêm các ngôn ngữ bạn cần deploy vào lệnh setup:static-content:deploy
RUN php bin/magento setup:di:compile
RUN php bin/magento setup:static-content:deploy -f en_US vi_VN # Thêm các locale khác nếu cần

# Thiết lập quyền sở hữu và quyền truy cập
# www-data là user/group mặc định của image php-fpm chính thức
# Kiểm tra lại nếu bạn dùng base image khác
RUN chown -R www-data:www-data /var/www/magento

# Image base đã có EXPOSE 9000 và CMD ["php-fpm"] nên không cần thêm lại ở đây
# Dọn dẹp cache composer nếu muốn giảm kích thước image (tùy chọn)
# RUN rm -rf /root/.composer/cache