FROM docker.io/bitnami/wordpress-nginx:6.6.2

ARG APP_UID
ARG ENVIRONMENT

USER root

RUN groupmod -g $APP_UID www-data && \
  usermod -u $APP_UID -g $APP_UID www-data

RUN chown -R www-data:root /opt/bitnami/nginx/tmp
RUN chown -R www-data:root /opt/bitnami/wordpress
RUN chown -R www-data:root /bitnami/wordpress
RUN chown -R www-data:root /opt/bitnami/wordpress/wp-content/languages
RUN chmod 777 -R /opt/bitnami/php/var/run/
COPY --chown=www-data:root ./build/docker/wordpress/entrypoint.sh /opt/bitnami/scripts/presspack.sh
COPY --chown=root:root ./build/docker/php-fpm/www.conf /opt/bitnami/php/etc/php-fpm.d/www.conf
COPY --chown=root:root ./build/docker/nginx/default.conf /opt/bitnami/nginx/conf/nginx.conf

RUN mv /opt/bitnami/nginx/conf/server_blocks/wordpress-https-server-block.conf /opt/bitnami/nginx/conf/server_blocks/wordpress-https-server-block.conf.old

COPY ./.env /opt/bitnami/wordpress/.env

# COPY ./.env.dev /opt/bitnami/wordpress/.env.dev
# COPY ./.env.staging /opt/bitnami/wordpress/.env.staging
# COPY ./.env.production /opt/bitnami/wordpress/.env.production

# Remove default plugins added by bitnami image.
#
# ** If needed, add those to composer.json**
#
# akismet amp jetpack
# all-in-one-seo-pack google-analytics-for-wordpress simple-tags
# all-in-one-wp-migration hello.php w3-total-cache
# amazon-polly wp-mail-smtp
RUN rm -rf /opt/bitnami/wordpress/wp-content/plugins/akismet /opt/bitnami/wordpress/wp-content/plugins/amp /opt/bitnami/wordpress/wp-content/plugins/jetpack /opt/bitnami/wordpress/wp-content/plugins/all-in-one-seo-pack /opt/bitnami/wordpress/wp-content/plugins/google-analytics-for-wordpress /opt/bitnami/wordpress/wp-content/plugins/simple-tags /opt/bitnami/wordpress/wp-content/plugins/all-in-one-wp-migration /opt/bitnami/wordpress/wp-content/plugins/hello.php /opt/bitnami/wordpress/wp-content/plugins/w3-total-cache /opt/bitnami/wordpress/wp-content/plugins/amazon-polly /opt/bitnami/wordpress/wp-content/plugins/wp-mail-smtp
# END Plugins removal

RUN rm -rf /opt/bitnami/wordpress/wp-content/themes/twenty*
COPY --chown=www-data:www-data ./wp-content /opt/bitnami/wordpress/wp-content
COPY --chown=www-data:www-data ./config/wp-config.php /opt/bitnami/wordpress/wp-config.php
COPY --chown=www-data:www-data ./vendor /opt/bitnami/wordpress/vendor
COPY --chown=www-data:www-data ./.htaccess /opt/bitnami/wordpress/.htaccess
COPY --chown=www-data:www-data ./build/docker/wordpress/wordpress-php.ini /opt/bitnami/php/etc/conf.d/wordpress-php.ini

# RUN if [[ "$ENVIRONMENT" = development ]] ; then mv /opt/bitnami/wordpress/.env.dev /opt/bitnami/wordpress/.env ; fi
# RUN if [[ "$ENVIRONMENT" = staging ]] ; then mv /opt/bitnami/wordpress/.env.staging /opt/bitnami/wordpress/.env ; fi
# RUN if [[ "$ENVIRONMENT" = production ]] ; then mv /opt/bitnami/wordpress/.env.production /opt/bitnami/wordpress/.env ; fi
# RUN mv /opt/bitnami/wordpress/.env /opt/bitnami/wordpress/.env

USER www-data
RUN chmod +x /opt/bitnami/scripts/presspack.sh
CMD [ "/opt/bitnami/scripts/presspack.sh"]


