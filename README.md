[![License: MIT](https://img.shields.io/github/license/siewert87/aaas-api.svg)](https://opensource.org/licenses/MIT)
[![Travis Status](https://img.shields.io/travis/siewert87/aaas-api.svg?label=Build)](https://travis-ci.org/siewert87/aaas-api)
![Code Size](https://img.shields.io/github/languages/code-size/siewert87/aaas-api.svg)
[![Latest Tag](https://img.shields.io/github/tag/siewert87/aaas-api.svg?label=Latest)](https://github.com/siewert87/aaas-api/releases)

# API as a Service - API

This is the _API as a Service_ API.

With _API as a Service_ you can easily build PHP APIs via a GUI.

## Table of Contents

1. [Essential](#essential)
2. [Start Containers](#start-containers)
3. [Visit Docs](#visit-docs)
4. [Useful commands for development](#useful-commands-for-development)
5. [Continuous Integration](#continuous-integration)

## Essential

#### Requirements

* [Docker and Docker Compose]

#### Configuration

Application configuration is stored in `.env` file. 

#### Application environment
You can change application environment to `dev` of `prod` by changing `APP_ENV` variable in `.env` file.

#### Database and credentials
An `app` database is created by default with user `app` and password `app`. root password is `app`.

## Installation

### Start Containers 

```bash
docker-compose up
```

### Build Backend

#### Install dependencies

```bash
docker-compose exec php composer update
```

#### Generate JWT Certificate

```bash
$ docker-compose exec php openssl genrsa -out config/jwt/private.pem -aes256 4096
$ docker-compose exec php openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

Don't forget to update `JWT_PASSPHRASE` in `.env` file. By default it's `app!`

### Deploy Database Schema

Update database credentials in `.env` and deploy schema:

```bash
docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction
```

If you want to modify the entities, don't forget to run:

```bash
docker-compose exec php bin/console doctrine:migrations:diff
```

If you want to load the fixtures run:

```bash
docker-compose exec mariadb sh -c "mysql -u app -p app < /app/docs/db_fixtures.sql"
```

## Visit Docs

For [Swagger UI] open https://localhost in your browser.

## Useful commands for development

It is recommended to add short aliases for the following frequently used container commands:

* `docker-compose exec php /bin/bash` to run a shell inside the php container
* `docker-compose exec php php` to run php in container
* `docker-compose exec php bin/console` to run Symfony CLI commands
* `docker-compose exec php bin/console cache:clear` to clear cache
* `docker-compose exec php composer update` to update composer dependencies
* `docker-compose exec mariadb mysql -u app -p app` to run MySQL commands

## Continuous Integration

#### Running PHPUnit Tests

```bash
docker-compose exec php bin/phpunit
```

#### Generate PHP CodeSniffer XML Report

```bash
docker-compose exec php vendor/bin/phpcs --report=xml --report-file=docs/phpcs.xml
```

#### Generate PHPUnit Code Coverage HTML Report

```bash
docker-compose exec php php bin/phpunit --coverage-html docs/coverage
```

#### Generate PHP Mess Detector HTML Report

```bash
docker-compose exec php vendor/bin/phpmd src/ html phpmd.xml.dist --reportfile docs/phpmd.html
```

#### Generate PHP Depend Metrics

```bash
docker-compose exec php vendor/bin/pdepend --summary-xml=docs/php-pdepend.xml \
--jdepend-chart=docs/php-jdepend.svg --overview-pyramid=docs/php-pyramid.svg \
--ignore=src/Migrations/ src/
```

[Docker and Docker Compose]: https://docs.docker.com/engine/installation
[Swagger UI]: https://swagger.io/tools/swagger-ui/
