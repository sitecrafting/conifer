#!/bin/bash

POSITIONAL=()
while [[ $# -gt 0 ]]
do
key="$1"

case $key in
    -n|--non-interactive)
    INTERACTIVE=NO
    shift # past argument
    ;;
    *)    # unknown option
    POSITIONAL+=("$1") # save it in an array for later
    shift # past argument
    ;;
esac
done
set -- "${POSITIONAL[@]}" # restore positional parameters

if [[ $CI = true ]] ; then
	# are we in a CI environment?
	echo 'forcing non-interactive mode for CI environment'
	INTERACTIVE='NO'
else
	# not in a CI environment, default to interactive mode
	INTERACTIVE=${INTERACTIVE:-'YES'}
fi

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
    if [[ $INTERACTIVE = 'YES' ]] ; then

      #
      # Normal/default interactive mode: prompt the user for WP settings
      #

      read -p "${BOLD}Site URL${NORMAL} (https://conifer.lndo.site): " URL
      URL=${URL:-'https://conifer.lndo.site'}

      read -p "${BOLD}Site Title${NORMAL} (Conifer): " TITLE
      TITLE=${TITLE:-'Conifer'}

      # Determine the default username/email to suggest based on git config
      DEFAULT_EMAIL=$(git config --global user.email)
      DEFAULT_EMAIL=${DEFAULT_EMAIL:-'admin@example.com'}
      DEFAULT_USERNAME=$(echo $DEFAULT_EMAIL | sed 's/@.*$//')

      read -p "${BOLD}Admin username${NORMAL} ($DEFAULT_USERNAME): " ADMIN_USER
      ADMIN_USER=${ADMIN_USER:-"$DEFAULT_USERNAME"}

      read -p "${BOLD}Admin password${NORMAL} (conifer): " ADMIN_PASSWORD
      ADMIN_PASSWORD=${ADMIN_PASSWORD:-'conifer'}

      read -p "${BOLD}Admin email${NORMAL} ($DEFAULT_EMAIL): " ADMIN_EMAIL
      ADMIN_EMAIL=${ADMIN_EMAIL:-"$DEFAULT_EMAIL"}

    else

      #
      # NON-INTERACTIVE MODE
      # ONE DOES NOT SIMPLY PROMPT TRAVIS CI FOR USER PREFERENCES
      #

      URL='http://conifer.lndo.site'
      TITLE='Conifer'
      ADMIN_USER='conifer'
      ADMIN_PASSWORD='conifer'
      ADMIN_EMAIL='conifer+travisci@sitecrafting.com'

    fi

    # install WordPress
    wp core install \
      --url="$URL" \
      --title="$TITLE" \
      --admin_user="$ADMIN_USER" \
      --admin_password="$ADMIN_PASSWORD" \
      --admin_email="$ADMIN_EMAIL" \
      --skip-email
  fi

  # configure plugins and theme
  uninstall_plugins hello akismet
  wp --quiet plugin install --activate timber-library
  wp --quiet plugin activate conifer
  wp --quiet theme activate groot

  # install test themes
  rsync --archive $LANDO_MOUNT/test/themes/ $LANDO_MOUNT/wp/wp-content/themes/

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
