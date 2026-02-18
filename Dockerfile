FROM trafex/wordpress:latest

ARG APP_UID
ARG APP_GID

USER root

# Alpine: addgroup/adduser
RUN deluser www-data 2>/dev/null || true && \
  delgroup www-data 2>/dev/null || true && \
  addgroup -g ${APP_GID:-1000} www-data && \
  adduser -u ${APP_UID:-1000} -G www-data -D -s /bin/sh www-data

# Copy WordPress core from trafex location to our location
RUN cp -r /usr/src/wordpress/* /var/www/ 2>/dev/null || true && \
  rm -rf /var/www/wp-content || true

# Directories for trafex image structure (PHP-FPM needs /var/log/php84 or it fails as www-data)
RUN mkdir -p /var/www/wp-content/languages && \
  mkdir -p /var/run/php && \
  mkdir -p /var/log/php84 && \
  mkdir -p /var/lib/nginx/logs && \
  mkdir -p /var/cache/nginx && \
  mkdir -p /var/run/nginx && \
  mkdir -p /var/log/nginx && \
  chown -R www-data:www-data /var/www && \
  chown -R www-data:www-data /var/lib/nginx && \
  chown -R www-data:www-data /var/cache/nginx && \
  chown -R www-data:www-data /var/run/nginx && \
  chown -R www-data:www-data /var/log/nginx && \
  chown -R www-data:www-data /var/log/php84 && \
  chmod 777 /var/run/php

COPY --chown=www-data:www-data ./build/docker/wordpress/entrypoint.sh /usr/local/bin/presspack.sh
COPY --chown=root:root ./build/docker/php-fpm/www.conf /etc/php84/php-fpm.d/www.conf
COPY --chown=root:root ./build/docker/nginx/default.conf /etc/nginx/nginx.conf

# Remove trafex defaults that override our pool (zzz_custom.conf has pm.max_children=100 → timeout/504)
RUN rm -f /etc/nginx/http.d/default.conf \
  && rm -f /etc/php84/php-fpm.d/zzz_custom.conf

COPY ./.env /var/www/.env

# Remove default plugins (add to composer if needed)
RUN rm -rf /var/www/wp-content/plugins/akismet /var/www/wp-content/plugins/amp \
  /var/www/wp-content/plugins/jetpack /var/www/wp-content/plugins/all-in-one-seo-pack \
  /var/www/wp-content/plugins/google-analytics-for-wordpress /var/www/wp-content/plugins/simple-tags \
  /var/www/wp-content/plugins/all-in-one-wp-migration /var/www/wp-content/plugins/hello.php \
  /var/www/wp-content/plugins/w3-total-cache /var/www/wp-content/plugins/amazon-polly \
  /var/www/wp-content/plugins/wp-mail-smtp 2>/dev/null || true

RUN rm -rf /var/www/wp-content/themes/twenty* 2>/dev/null || true

COPY --chown=www-data:www-data ./wp-content /var/www/wp-content
COPY --chown=www-data:www-data ./config/wp-config.php /var/www/wp-config.php
COPY --chown=www-data:www-data ./vendor /var/www/vendor
COPY --chown=www-data:www-data ./.htaccess /var/www/.htaccess
COPY --chown=root:root ./build/docker/wordpress/wordpress-php.ini /etc/php84/conf.d/wordpress-php.ini

USER www-data
RUN chmod +x /usr/local/bin/presspack.sh

# Use nginx stub /status so healthcheck doesn't hit PHP (avoids worker pile-up and 504)
HEALTHCHECK --interval=30s --timeout=5s --start-period=40s --retries=3 \
  CMD wget -q -O- http://127.0.0.1:8080/status || exit 1

ENTRYPOINT ["/usr/local/bin/presspack.sh"]
