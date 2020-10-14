#!/bin/bash

POSITIONAL=()
while [[ $# -gt 0 ]]
do
key="$1"

case $key in
    --timber-version)
    TIMBER_VERSION="$2"
    shift # past argument
    shift # past argument
    ;;
    *)    # unknown option
    POSITIONAL+=("$1") # save it in an array for later
    shift # past argument
    ;;
esac
done
set -- "${POSITIONAL[@]}" # restore positional parameters

# Install and configure WordPress if we haven't already
main() {
  BOLD=$(tput bold)
  NORMAL=$(tput sgr0)

  WP_DIR="$LANDO_MOUNT/wp"

  if ! [[ -d "$WP_DIR"/wp-content/plugins/conifer ]] ; then
    echo 'Linking conifer plugin directory...'
    ln -s "../../../" "$WP_DIR"/wp-content/plugins/conifer
  fi

  echo 'Checking for WordPress config...'
  if wp_configured ; then
    echo 'WordPress is configured'
  else
    read -d '' extra_php <<'EOF'
// log all notices, warnings, etc.
error_reporting(E_ALL);

// enable debug logging
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
EOF

    # create a wp-config.php
    wp config create \
      --dbname="wordpress" \
      --dbuser="wordpress" \
      --dbpass="wordpress" \
      --dbhost="database" \
      --extra-php < <(echo "$extra_php")
  fi

  echo 'Checking for WordPress installation...'
  if wp_installed ; then
    echo 'WordPress is installed'
  else
    # install WordPress
    wp core install \
      --url='http://conifer.lndo.site' \
      --title='Conifer' \
      --admin_user='conifer' \
      --admin_password='conifer' \
      --admin_email='conifer@coniferplug.in' \
      --skip-email
  fi

  # configure plugins and theme
  uninstall_plugins hello akismet
  wp --quiet plugin activate conifer
  wp --quiet theme activate groot

  # install a specific version of Timber if necessary
  if [[ "$TIMBER_VERSION" ]] ; then
    composer require --dev timber/timber:"$TIMBER_VERSION"
  fi

  # install test themes
  rsync --archive --recursive $LANDO_MOUNT/test/themes/ $LANDO_MOUNT/wp/wp-content/themes/

  # uninstall stock themes
  wp --quiet theme uninstall \
    twentyten \
    twentyeleven \
    twentytwelve \
    twentythirteen \
    twentyfourteen \
    twentyfifteen \
    twentysixteen \
    twentyseventeen

  wp option set permalink_structure '/%postname%/'
  wp rewrite flush

  echo
  echo 'Done setting up!'
  echo
  echo 'Your WP username is: conifer'
  echo 'Your password is: conifer'
  echo

}


# Detect whether WP has been configured already
wp_configured() {
  [[ $(wp config path 2>/dev/null) ]] && return
  false
}

# Detect whether WP is installed
wp_installed() {
  wp --quiet core is-installed
  [[ $? = '0' ]] && return
  false
}

uninstall_plugins() {
  for plugin in $1 ; do
    wp plugin is-installed $plugin 2>/dev/null
    if [[ "$?" = "0" ]] ; then
      wp plugin uninstall $plugin
    fi
  done
}


main
