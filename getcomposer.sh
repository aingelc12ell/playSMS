#!/bin/sh

PATHSRC=$(pwd)

echo
echo "Getting composer from https://getcomposer.org"
echo
echo "Please wait while this script downloading composer"
echo

rm -f ./composer ./composer.phar

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
php composer-setup.php
php -r "unlink('composer-setup.php');"

if [ -e "./composer.phar" ]; then
	ln -s ./composer.phar ./composer >/dev/null 2>&1
	chmod +x ./composer.phar >/dev/null 2>&1
fi

echo "Composer has been installed"
echo
echo "Please wait while composer getting and updating required packages"
echo

if [ -x "./composer.phar" ]; then
	cd "$PATHSRC"
	./composer.phar update
	exit $?
else
	echo "ERROR: unable to get composer from https://getcomposer.org"
	exit 1
fi
