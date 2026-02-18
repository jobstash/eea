#!/usr/bin/env bash
echo "Presspack Entrypoint (trafex)"
echo "WordPress will be installed. Please wait until it finishes."

wp core install --path="/var/www" --url="http://localhost" --title="Local Wordpress" --admin_user=admin --admin_password=wordpress --admin_email=dev@localhost.local 2>/dev/null || echo "WordPress is already installed."

echo "Activating plugins"
wp plugin activate advanced-custom-fields-pro --path="/var/www" 2>/dev/null || true

echo "Activating wp-starter theme"
wp theme activate wp-starter --path="/var/www"

# wp rewrite structure /%postname%/ --path="/var/www"

echo "Presspack entrypoint end. Starting nginx and php-fpm ..."
/usr/sbin/php-fpm84 -D || echo "PHP-FPM start failed"

exec nginx -g 'daemon off;'
