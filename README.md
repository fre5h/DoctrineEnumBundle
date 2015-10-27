# DoctrineEnumBundle

Provides support of **ENUM type** for Doctrine in Symfony applications.

[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/fre5h/DoctrineEnumBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/fre5h/DoctrineEnumBundle/)
[![Build Status](https://img.shields.io/travis/fre5h/DoctrineEnumBundle.svg?style=flat-square)](https://travis-ci.org/fre5h/DoctrineEnumBundle)
[![CodeCov](https://img.shields.io/codecov/c/github/fre5h/DoctrineEnumBundle.svg?style=flat-square)](https://codecov.io/github/fre5h/DoctrineEnumBundle)
[![License](https://img.shields.io/packagist/l/fresh/doctrine-enum-bundle.svg?style=flat-square)](https://packagist.org/packages/fresh/doctrine-enum-bundle)
[![Latest Stable Version](https://img.shields.io/packagist/v/fresh/doctrine-enum-bundle.svg?style=flat-square)](https://packagist.org/packages/fresh/doctrine-enum-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/fresh/doctrine-enum-bundle.svg?style=flat-square)](https://packagist.org/packages/fresh/doctrine-enum-bundle)
[![Dependency Status](https://img.shields.io/versioneye/d/php/fresh:doctrine-enum-bundle.svg?style=flat-square)](https://www.versioneye.com/user/projects/550402de4a10647277000002)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/0cff4816-374a-474e-a1d5-9d5db34562e3.svg?style=flat-square)](https://insight.sensiolabs.com/projects/0cff4816-374a-474e-a1d5-9d5db34562e3)
[![Gitter](https://img.shields.io/badge/gitter-join%20chat-brightgreen.svg?style=flat-square)](https://gitter.im/fre5h/DoctrineEnumBundle?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

[![knpbundles.com](http://knpbundles.com/fre5h/DoctrineEnumBundle/badge-short)](http://knpbundles.com/fre5h/DoctrineEnumBundle)

## Supported platforms

* MySQL
* SQLite
* PostgreSQL

## Requirements

* PHP 5.4 *and later*
* Symfony 2.5 *and later*
* Doctrine 2.2 *and later*

## Installation

### Install via Composer

```php composer.phar require fresh/doctrine-enum-bundle='v3.2'```

### Register the bundle

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

### Update config.yml

Add the following lines for doctrine configuration in `config.yml` file:

```yml
# Doctrine Configuration
doctrine:
    dbal:
        mapping_types:
            enum: string
```

## Using

### Example

In this example will be shown how to create custom ENUM field for basketball positions. This ENUM should contain five values:

* `PG` - Point guard
* `SG` - Shooting guard
* `SF` - Small forward
* `PF` - Power forward
* `C`  - Center

Create class for new ENUM type `BasketballPositionType`:

```php
<?php
namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class BasketballPositionType extends AbstractEnumType
{
    const POINT_GUARD    = 'PG';
    const SHOOTING_GUARD = 'SG';
    const SMALL_FORWARD  = 'SF';
    const POWER_FORWARD  = 'PF';
    const CENTER         = 'C';

    protected static $choices = [
        self::POINT_GUARD    => 'Point guard',
        self::SHOOTING_GUARD => 'Shooting guard',
        self::SMALL_FORWARD  => 'Small forward',
        self::POWER_FORWARD  => 'Power forward',
        self::CENTER         => 'Center'
    ];
}
```

Register `BasketballPositionType` for Doctrine in config.yml:

```yml
# Doctrine Configuration
doctrine:
    dbal:
        types:
            BasketballPositionType: AppBundle\DBAL\Types\BasketballPositionType
```

Create `Player` entity that has `position` field:

```php
<?php
namespace AppBundle\Entity;

use AppBundle\DBAL\Types\BasketballPositionType;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="players")
 */
class Player
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Note, that type of field should be same as you set in doctrine config in this case it is BasketballPositionType
     *
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\BasketballPositionType")
     * @ORM\Column(name="position", type="BasketballPositionType", nullable=false)
     */
    protected $position;

    public function getId()
    {
        return $this->id;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }
}
```

Now you can set a position for `Player` inside some action or somewhere else:

```php
$player->setPosition(BasketballPositionType::POINT_GUARD);
```

But don't forget to define `BasketballPositionType` in the *use* section:

```php
use AppBundle\DBAL\Types\BasketballPositionType;
```

`NULL` values are also supported by ENUM field.
You can set *nullable* parameter of column to `true` or `false` depends on if you want or not to allow `NULL` values:

```php
/** @ORM\Column(name="position", type="BasketballPositionType", nullable=true) */
protected $position;
// or
/** @ORM\Column(name="position", type="BasketballPositionType", nullable=false) */
protected $position;
```

##### Building the form
When build `BasketballPositionType` as form field, you don't need to specify some additional parameters. Just add property to the form builder and [EnumTypeGuesser](./Form/EnumTypeGuesser.php "EnumTypeGuesser") will do all work for you. That's how:

```php
$builder->add('position');
```

If you need to add some extra parameters, just skip the second (`field type`) parameter:

```php
$builder->add('position', null, [
    'required' => true,
    'attr'     => [
        'class' => 'some-class'
    ]
]);
```

If for some reason you need to specify full config, it can look like this:
```php
$builder->add('position', 'choice', [
    'choices' => BasketballPositionType::getChoices()
]);
```

[EnumTypeGuesser](./Form/EnumTypeGuesser.php "EnumTypeGuesser") process **only** DBAL types that are children of [AbstractEnumType](./DBAL/Types/AbstractEnumType.php "AbstractEnumType").
All other custom DBAL types, which are defined, will be skipped from guessing.

##### Additional methods

[AbstractEnumType](./DBAL/Types/AbstractEnumType.php "AbstractEnumType") provides few additional methods, which might be useful.

If you need to check if some string value exists in the array of ENUM values:

```php
BasketballPositionType::isValueExist('Pitcher'); // false
```

If you need to get value in readable format:

```php
BasketballPositionType::getReadableValue(BasketballPositionType::SHOOTING_GUARD);
// Will output: Shooting guard
```

##### Readable ENUM values in templates
You would want to show ENUM values rendered in your templates in *readable format* instead of the values that would be stored in DB. It is easy to do by using the Twig filter `|readable` that was implemented for that purpose. In the example below if the player is a point guard of his team then his position will be rendered in template as `Point guard` instead of `PG`.

```jinja
{{ player.position|readable }}
```

How it works? If there is no additional parameter for the filter, [ReadableEnumValueExtension](./Twig/Extension/ReadableEnumValueExtension.php "ReadableEnumValueExtension") tries to find which ENUM type of the registered ENUM types consists this value. If only one ENUM type found, then it is possible to get the readable value from it. Otherwise it will throw an exception.

For example `BasketballPositionType` and `MapLocationType` can have same ENUM value `C` with its readable variant `Center`. The code below will throw an exception, because without additional parameter for `|readable` filter, it can't determine which ENUM type to use in which case:

```jinja
{{ set player_position = 'C' }}
{{ set location_on_the_map = 'C' }}

{{ player_position|readable }}
{{ location_on_the_map|readable }}
```

So, that correct usage of `|readable` filter in this case should be with additional parameter that specifies the ENUM type:

```jinja
{{ set player_position = 'C' }}
{{ set location_on_the_map = 'C' }}

{{ player_position|readable('BasketballPositionType') }}
{{ location_on_the_map|readable('MapLocationType') }}
```

### Hook for Doctrine migrations

If you use [Doctrine migrations](https://github.com/doctrine/migrations "Doctrine migrations") in your project you should be able to create migrations for you custom ENUM types. If you want to create migration for the **new** ENUM type, then just use console commands `doctrine:migrations:diff` to create migration and `doctrine:migrations:migrate` to execute it.

For the previous example of `BasketballPositionType` for MySQL DB (e.g.) Doctrine will generate SQL statement, that looks like this:

```sql
CREATE TABLE players (
    id INT AUTO_INCREMENT NOT NULL,
    position ENUM('PG', 'SG', 'SF', 'PF', 'C') NOT NULL COMMENT '(DC2Type:BasketballPositionType)',
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
```

You can see here the comment *'(DC2Type:BasketballPositionType)'* for `position` column. Doctrine will know that this column should be processed as `BasketballPositionType`.

If you later will need to add new values to ENUM or delete some existed, you also will need to create new migrations. But Doctrine won't detect any changes in your ENUM... :(

Fortunately you can do simple **hook** =) Access your database and delete comment for `position` column. After that run console command `doctrine:migrations:diff` it will create correct migrations.

You should repeat these steps after each update of your custom ENUM type!
