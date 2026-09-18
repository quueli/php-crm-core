# php-crm-core

![ci](https://github.com/quueli/php-crm-core/actions/workflows/ci.yml/badge.svg)

entities and repositories from a symfony/doctrine crm i did for a catalog-type business. no controllers, no templates, just the data model and the queries that were annoying to get right.

    composer install
    php bin/console doctrine:migrations:migrate     # migrations/ has the schema
    vendor/bin/phpunit                              # tree logic only, no db
