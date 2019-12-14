FROM webdevops/php-nginx:7.4
ARG APP_ENV=production
ENV APP_ENV "$APP_ENV"
ENV fpm.pool.clear_env no
ENV fpm.pool.pm=ondemand
ENV fpm.pool.pm.max_children=50
ENV fpm.pool.pm.process_idle_timeout=10s
ENV fpm.pool.pm.max_requests=500
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_NO_INTERACTION 1

COPY ./config/nginx/vhost/*.conf /opt/docker/etc/nginx/vhost.common.d/

WORKDIR /tmp
RUN apt-get update && apt-get -y install procps mcedit bsdtar libaio1 musl-dev

# Install Composer
RUN wget -O composer-setup.php --progress=bar:force https://getcomposer.org/installer \
    && php composer-setup.php --install-dir=/usr/bin --version=1.9.0 \
    && rm -f composer-setup.php

# Run APP
COPY --chown=1000:1000 ./src /app
WORKDIR /app
RUN chmod 0777 -R storage
RUN chown -R www-data:www-data storage
RUN if [ "$APP_ENV" = "development" ]; then composer install; else composer install --no-dev --optimize-autoloader; fi
RUN (crontab -l ; echo "* * * * * /usr/local/bin/php /app/artisan schedule:run --env="$APP_ENV" >> /dev/null 2>&1") | crontab
