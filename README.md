# DoctrineEnumBundle

Provides support of **ENUM type** for Doctrine in Symfony applications.

[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/fre5h/DoctrineEnumBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/fre5h/DoctrineEnumBundle/)
[![Build Status](https://travis-ci.org/fre5h/DoctrineEnumBundle.svg?branch=master&style=flat-square)](https://travis-ci.org/fre5h/DoctrineEnumBundle)
[![CodeCov](https://img.shields.io/codecov/c/github/fre5h/DoctrineEnumBundle.svg?style=flat-square)](https://codecov.io/github/fre5h/DoctrineEnumBundle)
[![License](https://img.shields.io/packagist/l/fresh/doctrine-enum-bundle.svg?style=flat-square)](https://packagist.org/packages/fresh/doctrine-enum-bundle)
[![Latest Stable Version](https://img.shields.io/packagist/v/fresh/doctrine-enum-bundle.svg?style=flat-square)](https://packagist.org/packages/fresh/doctrine-enum-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/fresh/doctrine-enum-bundle.svg?style=flat-square)](https://packagist.org/packages/fresh/doctrine-enum-bundle)
[![StyleCI](https://styleci.io/repos/6553368/shield?style=flat-square)](https://styleci.io/repos/6553368)
[![Gitter](https://img.shields.io/badge/gitter-join%20chat-brightgreen.svg?style=flat-square)](https://gitter.im/fre5h/DoctrineEnumBundle)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0cff4816-374a-474e-a1d5-9d5db34562e3/small.png)](https://insight.sensiolabs.com/projects/0cff4816-374a-474e-a1d5-9d5db34562e3)
[![knpbundles.com](http://knpbundles.com/fre5h/DoctrineEnumBundle/badge-short)](http://knpbundles.com/fre5h/DoctrineEnumBundle)

## Supported platforms

| MySQL | SQLite | PostgreSQL | MSSQL |
|-------|--------|------------|-------|

## Installation

### Add dependency via Composer

```composer require fresh/doctrine-enum-bundle='~5.1'```

##### Choose the appropriate version if you need

| Bundle Version (X.Y.Z) | PHP     | Symfony            | Doctrine DBAL | Comment        |
|:----------------------:|:-------:|:------------------:|:-------------:|:---------------|
| 6.0.*                  | >= 7.1  | >= 4.0             | >= 2.6        | *Coming soon*  |
| 5.1.*                  | >= 5.6  | >= 3.2             | >= 2.5        | **Actual version** |
| 4.8.*                  | >= 5.4  | >= 2.6, >= 3.0     | >= 2.2        | ~~Legacy version~~ |

### Register the bundle for Symfony2/3

To start using the bundle, register it in `app/AppKernel.php`:

```php
public function registerBundles()
{
    $bundles = [
        // Other bundles...
        new Fresh\DoctrineEnumBundle\FreshDoctrineEnumBundle(),
    ];
}
```

## Using

* [Example](./Resources/docs/example_of_using.md "Example")

## Features

* [NULL values](./Resources/docs/null_values.md "NULL values")
* [Building the form](./Resources/docs/building_the_form.md "Building the form")
* [Additional methods](./Resources/docs/additional_methods.md "Additional methods")
* [Readable ENUM values in templates](./Resources/docs/readable_enum_values_in_template.md "Readable ENUM values in templates")
* [ENUM constants in templates](./Resources/docs/enum_constants_in_templates.md "ENUM constants in templates")
* [Hook for Doctrine migrations](./Resources/docs/hook_for_doctrine_migrations.md "Hook for Doctrine migrations")

## Contributing

See [CONTRIBUTING](https://github.com/fre5h/DoctrineEnumBundle/blob/master/.github/CONTRIBUTING.md) file.
