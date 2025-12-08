FROM trafex/wordpress:latest

ARG APP_UID
ARG ENVIRONMENT

USER root

# Alpine Linux uses addgroup/adduser instead of groupmod/usermod
RUN deluser www-data 2>/dev/null || true && \
  delgroup www-data 2>/dev/null || true && \
  addgroup -g $APP_UID www-data && \
  adduser -u $APP_UID -G www-data -D -s /bin/sh www-data

# Copy WordPress core from trafex location to our location
RUN cp -r /usr/src/wordpress/* /var/www/ 2>/dev/null || true && \
  rm -rf /var/www/wp-content || true

# Create necessary directories for trafex image structure
RUN mkdir -p /var/www/wp-content/languages && \
  mkdir -p /var/run/php && \
  mkdir -p /var/lib/nginx/logs && \
  mkdir -p /var/cache/nginx && \
  mkdir -p /var/run/nginx && \
  mkdir -p /var/log/nginx && \
  chown -R www-data:www-data /var/www && \
  chown -R www-data:www-data /var/lib/nginx && \
  chown -R www-data:www-data /var/cache/nginx && \
  chown -R www-data:www-data /var/run/nginx && \
  chown -R www-data:www-data /var/log/nginx && \
  chmod 777 /var/run/php

# Copy entrypoint script (trafex uses /entrypoint.sh, we'll override it)
COPY --chown=www-data:www-data ./build/docker/wordpress/entrypoint.sh /usr/local/bin/welance.sh

# Copy PHP-FPM configuration (trafex uses /etc/php84/php-fpm.d/)
COPY --chown=root:root ./build/docker/php-fpm/www.conf /etc/php84/php-fpm.d/www.conf

# Copy Nginx configuration (trafex uses /etc/nginx/)
COPY --chown=root:root ./build/docker/nginx/default.conf /etc/nginx/nginx.conf

# Remove trafex default.conf that returns 404 for everything
RUN rm -f /etc/nginx/http.d/default.conf

# Copy environment files (trafex uses /var/www/ for WordPress)
COPY ./.env /var/www/.env

# Remove default plugins (e.g., akismet) that come with WordPress.
#
# ** If needed, add those to composer.json**
#
# akismet amp jetpack
# all-in-one-seo-pack google-analytics-for-wordpress simple-tags
# all-in-one-wp-migration hello.php w3-total-cache
# amazon-polly wp-mail-smtp
# Remove default plugins (if they exist)
RUN rm -rf /var/www/wp-content/plugins/akismet /var/www/wp-content/plugins/amp /var/www/wp-content/plugins/jetpack /var/www/wp-content/plugins/all-in-one-seo-pack /var/www/wp-content/plugins/google-analytics-for-wordpress /var/www/wp-content/plugins/simple-tags /var/www/wp-content/plugins/all-in-one-wp-migration /var/www/wp-content/plugins/hello.php /var/www/wp-content/plugins/w3-total-cache /var/www/wp-content/plugins/amazon-polly /var/www/wp-content/plugins/wp-mail-smtp 2>/dev/null || true
# END Plugins removal

RUN rm -rf /var/www/wp-content/themes/twenty* 2>/dev/null || true

# Copy application files (trafex uses /var/www/ for WordPress)
COPY --chown=www-data:www-data ./wp-content /var/www/wp-content
COPY --chown=www-data:www-data ./config/wp-config.php /var/www/wp-config.php
COPY --chown=www-data:www-data ./vendor /var/www/vendor
COPY --chown=www-data:www-data ./.htaccess /var/www/.htaccess
COPY --chown=root:root ./build/docker/wordpress/wordpress-php.ini /etc/php84/conf.d/wordpress-php.ini

USER www-data
RUN chmod +x /usr/local/bin/welance.sh

# Override trafex entrypoint with our custom script
ENTRYPOINT ["/usr/local/bin/welance.sh"]


