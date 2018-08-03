#!/usr/bin/env bash
# Provision Conifer on latest WordPress

# Make a database, if we don't already have one
echo -e "\nCreating database 'conifer' (if it's not already there)"
mysql -u root --password=root -e "CREATE DATABASE IF NOT EXISTS conifer"
mysql -u root --password=root -e "GRANT ALL PRIVILEGES ON conifer.* TO conifer@localhost IDENTIFIED BY 'conifer';"
echo -e "\n DB operations done.\n\n"

# Nginx Logs
mkdir -p ${VVV_PATH_TO_SITE}/log
touch ${VVV_PATH_TO_SITE}/log/error.log
touch ${VVV_PATH_TO_SITE}/log/access.log

cd ${VVV_PATH_TO_SITE}

# Install and configure the latest stable version of WordPress
if [[ ! -d "${VVV_PATH_TO_SITE}/wp" ]]; then

  noroot composer install

  echo "Configuring WordPress Stable..."
  noroot wp core config \
    --dbname=conifer \
    --dbuser=conifer \
    --dbpass=conifer \
    --quiet \
    --extra-php <<PHP
// Match any requests made via xip.io.
if ( isset( \$_SERVER['HTTP_HOST'] ) && preg_match('/^(conifer.wordpress.)\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}(.xip.io)\z/', \$_SERVER['HTTP_HOST'] ) ) {
    define( 'WP_HOME', 'http://' . \$_SERVER['HTTP_HOST'] );
    define( 'WP_SITEURL', 'http://' . \$_SERVER['HTTP_HOST'] );
}

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
PHP

  echo "Installing WordPress Stable..."
  noroot wp core install \
    --url=conifer.wordpress.test \
    --quiet \
    --title="Local Conifer Dev" \
    --admin_name=admin \
    --admin_email="admin@local.test" \
    --admin_password="password"

else

  echo "Updating WordPress Stable..."
  noroot wp core update

fi

echo 'Symlinking conifer plugin directory...'
ln -sf "${VVV_PATH_TO_SITE}" "${VVV_PATH_TO_SITE}/wp/wp-content/plugins/conifer"

echo 'Setting up Conifer & Groot (starter theme)'
noroot wp --quiet plugin install --activate timber-library
noroot wp --quiet plugin activate conifer
noroot wp --quiet theme activate groot

noroot wp --quiet theme uninstall \
  twentyten \
  twentyeleven \
  twentytwelve \
  twentythirteen \
  twentyfourteen \
  twentyfifteen \
  twentysixteen \
  twentyseventeen

noroot wp --quiet option set permalink_structure '/%postname%/'
noroot wp --quiet rewrite flush

echo 'Cleaning up...'
if [[ -d "${VVV_PATH_TO_SITE}/conifer" ]] ; then
  rm -rf "${VVV_PATH_TO_SITE}/conifer"
fi
