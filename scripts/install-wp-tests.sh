#!/usr/bin/env bash


usage() {
  echo "usage: $(basename $0) [--clean]"
  echo
  echo "  OPTIONS:"
  echo
  echo "  --clean remove any existing WP test suite installation first"
}

POSITIONAL=()
while [[ $# -gt 0 ]]
do
key="$1"

case $key in
    --clean)
    CLEAN=1
    shift # past argument
    ;;
    *)
    POSITIONAL+=("$1") # save it in an array for later
    shift # past argument
    ;;
esac

done
set -- "${POSITIONAL[@]}" # restore positional parameters


# NOTE: For local dev, these need to match the dbtest creds defined in the Landofile
DB_HOST=${DB_HOST:-'testdb'}
DB_NAME=${DB_NAME:-'test'}
DB_USER=${DB_USER:-'test'}
DB_PASS=${DB_PASS:-'test'}

WP_TESTS_DIR=test/wp-tests-lib
WP_CORE_DIR=test/wp
WP_TESTS_TAG='tags/5.5.1'

# deletes the entire tmp dir so we can reinstall test suite
cleanup() {
  echo 'Cleaning up any existing test install...'
  rm -rf $WP_TESTS_DIR $WP_CORE_DIR
}


install_wp() {

	if [ -d $WP_CORE_DIR ]; then
    echo 'WP test install exists, skipping.'
		return;
	fi

  echo 'Installing WP test core...'

	mkdir -p $WP_CORE_DIR

  curl -sL https://wordpress.org/latest.tar.gz > /tmp/wordpress.tar.gz
  tar --strip-components=1 -zxmf /tmp/wordpress.tar.gz -C $WP_CORE_DIR

	curl -sL https://raw.github.com/markoheijnen/wp-mysqli/master/db.php > $WP_CORE_DIR/wp-content/db.php

  echo 'Done.'
}

install_test_suite() {
	# set up testing suite if it doesn't yet exist
	if [ -d $WP_TESTS_DIR ]; then
    echo 'WP test suite is already installed, skipping.'
  else
    echo 'Installing WP test suite...'
		# set up testing suite
		mkdir -p $WP_TESTS_DIR
		svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/includes/ $WP_TESTS_DIR/includes
		svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/data/ $WP_TESTS_DIR/data
	fi

	if [ -f "$WP_TESTS_DIR"/wp-tests-config.php ]; then
    echo 'WP test config is already install, skipping.'
  else
    echo 'Installing WP test config...'
		curl -sL https://develop.svn.wordpress.org/${WP_TESTS_TAG}/wp-tests-config-sample.php > "$WP_TESTS_DIR"/wp-tests-config.php
		sed -i "s:dirname( __FILE__ ) . '/src/':'$WP_CORE_DIR/':" "$WP_TESTS_DIR"/wp-tests-config.php
		sed -i "s/youremptytestdbnamehere/$DB_NAME/" "$WP_TESTS_DIR"/wp-tests-config.php
		sed -i "s/yourusernamehere/$DB_USER/" "$WP_TESTS_DIR"/wp-tests-config.php
		sed -i "s/yourpasswordhere/$DB_PASS/" "$WP_TESTS_DIR"/wp-tests-config.php
		sed -i "s|localhost|${DB_HOST}|" "$WP_TESTS_DIR"/wp-tests-config.php
	fi

  echo 'Done.'

}

main() {
  if [ $CLEAN ]; then
    cleanup
  fi
  install_wp
  install_test_suite
}


main
