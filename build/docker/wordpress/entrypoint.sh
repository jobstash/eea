#!/usr/bin/env bash
echo "Presspack Entrypoint"
echo "Wordpress will be installed. Please wait until it finish!";
# sleep 30; // Needs to be turned on on the first install

wp core install --path="/opt/bitnami/wordpress" --url="http://localhost" --title="Local Wordpress" --admin_user=admin --admin_password=wordpress --admin_email=dev@localhost.local;

echo "Activating plugins";
wp plugin activate advanced-custom-fields-pro
wp plugin activate akismet

echo "Activating wp-starter Theme";
wp theme activate wp-starter

# echo "Rewrite permalinks structure to /%postname%/";
# wp rewrite structure /%postname%/

echo "Presspack entrypoint end. Starting /opt/bitnami/scripts/nginx-php-fpm/run.sh ...";
/opt/bitnami/scripts/nginx-php-fpm/run.sh



