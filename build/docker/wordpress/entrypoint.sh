#!/usr/bin/env bash
echo "Welance Entrypoint"
echo "Wordpress will be installed. Please wait until it finish!";
# sleep 30; // Needs to be turned on on the first install

# Set WordPress path for trafex image
wp core install --path="/var/www" --url="http://localhost" --title="Local Wordpress" --admin_user=admin --admin_password=wordpress --admin_email=dev@localhost.local 2>/dev/null || echo "WordPress is already installed.";

echo "Activating plugins";
wp plugin activate advanced-custom-fields-pro --path="/var/www"
wp plugin activate akismet --path="/var/www"

echo "Activating wp-starter Theme";
wp theme activate wp-starter --path="/var/www"

# echo "Rewrite permalinks structure to /%postname%/";
wp rewrite structure /%postname%/ --path="/var/www"

echo "Welance entrypoint end. Starting nginx and php-fpm ...";
# Start PHP-FPM in background (trafex uses php-fpm84)
/usr/sbin/php-fpm84 -D || echo "PHP-FPM start failed"

# Start nginx in foreground (keeps container running)
exec nginx -g 'daemon off;'



