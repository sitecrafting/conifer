#!/bin/bash

# Install and configure WordPress if we haven't already
main() {
  BOLD=$(tput bold)
  NORMAL=$(tput sgr0)

  WP_DIR="$LANDO_MOUNT/wp"

  if ! [[ -f "$WP_DIR"/wp-content/plugins/conifer ]] ; then
    echo 'Linking conifer plugin directory...'
    ln -s "../../../" "$WP_DIR"/wp-content/plugins/conifer
  fi

  echo 'Checking for WordPress config...'
  if wp_configured ; then
    echo 'WordPress is configured'
  else
    # TODO get this to work...
    extra_php=<<'EOF'

error_reporting(E_ALL);

// enable debug logging
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
EOF

    # create a wp-config.php
    wp --path="$WP_DIR" config create \
      --dbname="$DB_NAME" \
      --dbuser="$DB_USER" \
      --dbpass="$DB_PASSWORD" \
      --dbhost="$DB_HOST" \
      --extra-php < <(echo "$extra_php")
  fi

  echo 'Checking for WordPress installation...'
  if wp_installed ; then
    echo 'WordPress is installed'
  else
    read -p "${BOLD}Site URL${NORMAL} (https://conifer.lndo.site): " URL
    URL=${URL:-'https://conifer.lndo.site'}

    read -p "${BOLD}Site Title${NORMAL} (Conifer): " TITLE
    TITLE=${TITLE:-'Conifer'}

    read -p "${BOLD}Admin username${NORMAL} (admin): " ADMIN_USER
    ADMIN_USER=${ADMIN_USER:-'admin'}

    read -p "${BOLD}Admin password${NORMAL} (conifer): " ADMIN_PASSWORD
    ADMIN_PASSWORD=${ADMIN_PASSWORD:-'conifer'}

    read -p "${BOLD}Admin email${NORMAL} (conifer@example.com): " ADMIN_EMAIL
    ADMIN_EMAIL=${ADMIN_EMAIL:-'conifer@example.com'}

    # install WordPress
    wp --path="$WP_DIR" core install \
      --url="$URL" \
      --title="$TITLE" \
      --admin_user="$ADMIN_USER" \
      --admin_password="$ADMIN_PASSWORD" \
      --admin_email="$ADMIN_EMAIL" \
      --skip-email
  fi
}


# Detect whether WP has been configured already
wp_configured() {
  [[ $(wp --path=$WP_DIR config path 2>/dev/null) ]] && return
  false
}

# Detect whether WP is installed
wp_installed() {
  [[ $(wp --path=$WP_DIR core is-installed 2>/dev/null) ]] && return
  false
}


main
