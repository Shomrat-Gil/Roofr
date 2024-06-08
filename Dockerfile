FROM dwchiang/nginx-php-fpm:8.1.27-fpm-alpine3.18-nginx-1.25.4

WORKDIR /var/www/html

COPY --chown=www-data:www-data --chmod=755 src/myapp /var/www/html

# Download and install PHP extension installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions opcache pgsql pdo_pgsql redis

# Install required packages and Composer
RUN apk --no-cache add zip \
    git \
    less \
    vim \
    unzip \
    curl \
    bash \
    yarn \
    make && \
    curl -sS https://getcomposer.org/installer -o composer-setup.php && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    rm composer-setup.php

# Verify Composer installation
RUN composer --version

# Copy nginx configuration
COPY nginx/conf.d/app.conf /etc/nginx/conf.d/default.conf

# Change default shell for www-data user to bash
RUN sed -i -e "s/bin\/ash/bin\/bash/" /etc/passwd

# Switch to www-data user and install Composer dependencies
USER www-data
WORKDIR /var/www/html
RUN composer install -o

# Switch back to root user
USER root

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]