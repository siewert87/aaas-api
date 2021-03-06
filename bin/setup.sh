#!/bin/bash

# NOTE: So far, we only use this setup file for installation routines on Gitpod.
# Of course, you can also use the file for other platforms, but you will probably
# have to adapt it accordingly.

# Install databases
mysql -e 'CREATE DATABASE app;'
mysql -e 'CREATE DATABASE app_test;'

# Install Composer dependencies
composer install

# Generate OpenSSL certificate
openssl genrsa -out config/jwt/private.pem -aes256 -passout pass:app! 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem -passin pass:app!

# Generate OpenSSL certificate for our test suite
openssl genrsa -out config/jwt/private-test.pem -aes256 -passout pass:app! 4096
openssl rsa -pubout -in config/jwt/private-test.pem -out config/jwt/public-test.pem -passin pass:app!

# Populate database with schema
php bin/console doctrine:migrations:migrate --no-interaction

# Populate test database with schema and install fixtures
php bin/console doctrine:migrations:migrate --no-interaction --env=test
php bin/console doctrine:fixtures:load --no-interaction --env=test

# Run test suite
php bin/phpunit -c config/ci/phpunit.xml.dist