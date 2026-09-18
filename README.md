# php-crm-core

![ci](https://github.com/quueli/php-crm-core/actions/workflows/ci.yml/badge.svg)

entities and repositories from a symfony/doctrine crm i did for a catalog-type business. no controllers, no templates, just the data model and the queries that were annoying to get right.

- the category tree is self-referential; `buildHierarchicalTree()` loads everything in one query and wires it up in php instead of a query per level
- `findByNameLike()` lowercases in php with the mb_ functions because cyrillic + the db's default collation was a mess
- an item is a unique line x audience x context combo, `existsCombination()` checks that before an insert
- the attribute "matrix" is ItemCategory -> ItemCategoryAttributeValue, every placement carries its own attribute values

## use

its a library, drop it into a symfony app:

    composer install
    php bin/console doctrine:migrations:migrate     # migrations/ has the schema
    vendor/bin/phpunit                              # tree logic only, no db

php 8.1+, doctrine orm 2.
