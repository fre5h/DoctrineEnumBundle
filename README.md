# DoctrineEnumBundle

📦 Provides **ENUM type** support for Doctrine in Symfony applications.

[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/fre5h/DoctrineEnumBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/fre5h/DoctrineEnumBundle/)
[![Build Status](https://img.shields.io/github/actions/workflow/status/fre5h/DoctrineEnumBundle/ci.yaml?branch=main&style=flat-square)](https://github.com/fre5h/DoctrineEnumBundle/actions?query=workflow%3ACI+branch%3Amain+)
[![CodeCov](https://img.shields.io/codecov/c/github/fre5h/DoctrineEnumBundle.svg?style=flat-square)](https://codecov.io/github/fre5h/DoctrineEnumBundle)
[![License](https://img.shields.io/packagist/l/fresh/doctrine-enum-bundle.svg?style=flat-square)](https://packagist.org/packages/fresh/doctrine-enum-bundle)
[![Latest Stable Version](https://img.shields.io/packagist/v/fresh/doctrine-enum-bundle.svg?style=flat-square)](https://packagist.org/packages/fresh/doctrine-enum-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/fresh/doctrine-enum-bundle.svg?style=flat-square)](https://packagist.org/packages/fresh/doctrine-enum-bundle)
[![StyleCI](https://styleci.io/repos/6553368/shield?style=flat-square)](https://styleci.io/repos/6553368)
[![Gitter](https://img.shields.io/badge/gitter-join%20chat-brightgreen.svg?style=flat-square)](https://gitter.im/fre5h/DoctrineEnumBundle)

## Supported platforms 🧐

| PostgreSQL | SQLite | MySQL | MSSQL |
|------------|--------|-------|-------|

## Installation 🌱

```composer req fresh/doctrine-enum-bundle```

##### Choose the version you need

| Bundle Version (X.Y.Z) |    PHP   | Symfony  |  Doctrine Bundle  | Comment             |
|:----------------------:|:--------:|:--------:|:-----------------:|:--------------------|
|        `12.0.*`        | `>= 8.4` | `>= 7.4` | `>= 2.12, >= 3.0` | **Current version** |
|        `11.2.*`        | `>= 8.2` | `>= 6.4` | `>= 2.12, >= 3.0` | Previous            |

#### Check the `config/bundles.php` file

By default, Symfony Flex will add this bundle to the `config/bundles.php` file.
But in case you ignored `contrib-recipe` during bundle installation it would not be added. In this case add the bundle manually:

```php
# config/bundles.php

return [
    // Other bundles...
    Fresh\DoctrineEnumBundle\FreshDoctrineEnumBundle::class => ['all' => true],
    // Other bundles...
];
```

## Usage 🧑‍🎓

* [Example](./Resources/docs/usage_example.md "Example")

## Features 🎁

* [NULL values](./Resources/docs/null_values.md "NULL values")
* [Default value](./Resources/docs/default_value.md "Default value")
* [Building the form](./Resources/docs/building_the_form.md "Building the form")
* [Additional methods](./Resources/docs/additional_methods.md "Additional methods")
* [Common types](./Resources/docs/common_types.md "Common types")
* [Readable ENUM values in templates](./Resources/docs/readable_enum_values_in_template.md "Readable ENUM values in templates")
* [ENUM constants in templates](./Resources/docs/enum_constants_in_templates.md "ENUM constants in templates")
* [ENUM values in templates](./Resources/docs/enum_values_in_templates.md "ENUM values in templates")
* [Hook for Doctrine migrations](./Resources/docs/hook_for_doctrine_migrations.md "Hook for Doctrine migrations")

## Contributing 🤝

Read the [CONTRIBUTING](https://github.com/fre5h/DoctrineEnumBundle/blob/master/.github/CONTRIBUTING.md) file.
